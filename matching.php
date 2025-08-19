<?php
/**
 * AI Job Matching Algorithm
 * 
 * This script implements a simple vector-based job matching algorithm
 * that compares job seeker skills and interests with job requirements.
 * It then calculates a similarity score and stores it in the AIMatch table.
 */

include('../../connector.php');

/**
 * Calculate similarity between two texts using cosine similarity of word frequencies
 * 
 * @param string $text1 First text to compare
 * @param string $text2 Second text to compare
 * @return float Similarity score between 0 and 1
 */
function calculateSimilarity($text1, $text2) {
    // Convert texts to lowercase
    $text1 = strtolower($text1);
    $text2 = strtolower($text2);
    
    // Remove common punctuation
    $text1 = preg_replace('/[^\w\s]/', '', $text1);
    $text2 = preg_replace('/[^\w\s]/', '', $text2);
    
    // Extract words
    $words1 = explode(' ', $text1);
    $words2 = explode(' ', $text2);
    
    // Count word frequencies
    $freq1 = array_count_values($words1);
    $freq2 = array_count_values($words2);
    
    // Get unique words from both texts
    $uniqueWords = array_unique(array_merge(array_keys($freq1), array_keys($freq2)));
    
    // Calculate dot product
    $dotProduct = 0;
    foreach ($uniqueWords as $word) {
        $count1 = isset($freq1[$word]) ? $freq1[$word] : 0;
        $count2 = isset($freq2[$word]) ? $freq2[$word] : 0;
        $dotProduct += $count1 * $count2;
    }
    
    // Calculate magnitudes
    $magnitude1 = 0;
    foreach ($freq1 as $count) {
        $magnitude1 += $count * $count;
    }
    $magnitude1 = sqrt($magnitude1);
    
    $magnitude2 = 0;
    foreach ($freq2 as $count) {
        $magnitude2 += $count * $count;
    }
    $magnitude2 = sqrt($magnitude2);
    
    // Prevent division by zero
    if ($magnitude1 == 0 || $magnitude2 == 0) {
        return 0;
    }
    
    // Calculate cosine similarity
    $similarity = $dotProduct / ($magnitude1 * $magnitude2);
    
    return $similarity;
}

/**
 * Run the matching algorithm for all job seekers and active jobs
 */
function runMatching() {
    global $connect;
    
    echo "Starting AI job matching process...\n";
    
    // Get all job seekers
    $jobSeekerQuery = mysqli_query($connect, "
        SELECT u.id, u.name, cp.skills, cp.interests, cp.education, cp.experience
        FROM User u
        JOIN CareerProfile cp ON u.id = cp.user_id
        WHERE u.role = 'jobseeker' AND u.deleted = 0
    ");
    
    // Get all active jobs
    $jobsQuery = mysqli_query($connect, "
        SELECT j.id, j.title, j.description, j.requirements, j.company_id
        FROM JobListing j
        WHERE j.status = 'active'
    ");
    
    $jobs = [];
    while ($job = mysqli_fetch_assoc($jobsQuery)) {
        $jobs[] = $job;
    }
    
    // For each job seeker, calculate matches with all jobs
    while ($jobSeeker = mysqli_fetch_assoc($jobSeekerQuery)) {
        echo "Processing matches for job seeker: {$jobSeeker['name']} (ID: {$jobSeeker['id']})\n";
        
        // Combine all profile information for comprehensive matching
        $seekerProfile = $jobSeeker['skills'] . ' ' . $jobSeeker['interests'] . ' ' . 
                         $jobSeeker['education'] . ' ' . $jobSeeker['experience'];
        
        foreach ($jobs as $job) {
            // Combine job title, description and requirements for matching
            $jobProfile = $job['title'] . ' ' . $job['description'] . ' ' . $job['requirements'];
            
            // Calculate match score
            $score = calculateSimilarity($seekerProfile, $jobProfile);
            
            // Check if a match already exists
            $existingMatchQuery = mysqli_query($connect, "
                SELECT id FROM AIMatch 
                WHERE user_id = '{$jobSeeker['id']}' AND job_id = '{$job['id']}'
            ");
            
            if (mysqli_num_rows($existingMatchQuery) > 0) {
                // Update existing match
                $matchRow = mysqli_fetch_assoc($existingMatchQuery);
                mysqli_query($connect, "
                    UPDATE AIMatch 
                    SET score = '$score', match_date = NOW() 
                    WHERE id = '{$matchRow['id']}'
                ");
                echo "  Updated match with job ID {$job['id']}, score: " . round($score * 100) . "%\n";
            } else {
                // Insert new match
                mysqli_query($connect, "
                    INSERT INTO AIMatch (user_id, job_id, score, match_date)
                    VALUES ('{$jobSeeker['id']}', '{$job['id']}', '$score', NOW())
                ");
                echo "  Created new match with job ID {$job['id']}, score: " . round($score * 100) . "%\n";
            }
        }
    }
    
    echo "AI job matching process completed successfully!\n";
}

// Execute the matching algorithm
runMatching();
?>