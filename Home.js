import React from 'react';
import { Container, Row, Col, Card, Button } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Home() {
  const { isAuthenticated, isJobSeeker, isCompany } = useAuth();

  return (
    <>
      {/* Hero Section */}
      <div className="page-header text-center">
        <Container>
          <h1>AI-Powered Career Guidance</h1>
          <p className="lead">
            Connecting Rwandan youth with career opportunities through intelligent job matching
          </p>
          {!isAuthenticated && (
            <div className="mt-4">
              <Link to="/register" className="btn btn-light btn-lg me-3">
                Get Started
              </Link>
              <Link to="/login" className="btn btn-outline-light btn-lg">
                Sign In
              </Link>
            </div>
          )}
        </Container>
      </div>

      {/* Main Content */}
      <Container className="py-5">
        {isAuthenticated && (
          <Row className="mb-5">
            <Col md={12}>
              <Card className="border-0 shadow-sm">
                <Card.Body className="p-4">
                  <h2>Welcome Back!</h2>
                  <p className="lead">
                    {isJobSeeker 
                      ? 'Continue exploring job opportunities that match your profile.'
                      : 'Continue managing your job listings and applicants.'}
                  </p>
                  <div>
                    {isJobSeeker ? (
                      <>
                        <Link to="/dashboard" className="btn btn-primary me-3">
                          Go to Dashboard
                        </Link>
                        <Link to="/ai-matches" className="btn btn-outline-primary">
                          View AI Matches
                        </Link>
                      </>
                    ) : (
                      <>
                        <Link to="/company/dashboard" className="btn btn-primary me-3">
                          Go to Dashboard
                        </Link>
                        <Link to="/company/post-job" className="btn btn-outline-primary">
                          Post a Job
                        </Link>
                      </>
                    )}
                  </div>
                </Card.Body>
              </Card>
            </Col>
          </Row>
        )}

        {/* Features Section */}
        <h2 className="text-center mb-4">How It Works</h2>
        <Row className="g-4">
          <Col md={4}>
            <Card className="h-100 border-0 shadow-sm">
              <Card.Body className="p-4 text-center">
                <div className="mb-4">
                  <i className="bi bi-person-check text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h4>Create Your Profile</h4>
                <p>Build your professional profile with your skills, education, and career interests.</p>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="h-100 border-0 shadow-sm">
              <Card.Body className="p-4 text-center">
                <div className="mb-4">
                  <i className="bi bi-robot text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h4>AI Matching</h4>
                <p>Our AI analyzes your profile and matches you with the most suitable job opportunities.</p>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="h-100 border-0 shadow-sm">
              <Card.Body className="p-4 text-center">
                <div className="mb-4">
                  <i className="bi bi-briefcase text-primary" style={{ fontSize: '3rem' }}></i>
                </div>
                <h4>Apply and Connect</h4>
                <p>Apply to matched jobs and connect directly with employers seeking your talents.</p>
              </Card.Body>
            </Card>
          </Col>
        </Row>

        {/* Statistics Section */}
        <Row className="mt-5 py-5 bg-light rounded">
          <Col md={3} className="text-center">
            <h2 className="text-primary fw-bold">1,200+</h2>
            <p>Registered Job Seekers</p>
          </Col>
          <Col md={3} className="text-center">
            <h2 className="text-primary fw-bold">350+</h2>
            <p>Employer Companies</p>
          </Col>
          <Col md={3} className="text-center">
            <h2 className="text-primary fw-bold">500+</h2>
            <p>Active Job Listings</p>
          </Col>
          <Col md={3} className="text-center">
            <h2 className="text-primary fw-bold">800+</h2>
            <p>Successful Placements</p>
          </Col>
        </Row>

        {/* CTA Section */}
        <Row className="mt-5">
          <Col md={12} className="text-center">
            <h2>Ready to advance your career?</h2>
            <p className="lead mb-4">Join our platform today and discover opportunities tailored for you.</p>
            {!isAuthenticated && (
              <Link to="/register" className="btn btn-primary btn-lg">
                Sign Up Now
              </Link>
            )}
          </Col>
        </Row>
      </Container>
    </>
  );
}

export default Home;