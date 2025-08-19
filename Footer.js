import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import { Link } from 'react-router-dom';

function Footer() {
  return (
    <footer className="footer">
      <Container>
        <Row className="py-3">
          <Col md={4} className="mb-4">
            <h5>AI Career Guidance</h5>
            <p className="text-muted">
              Connecting Rwandan youth with opportunities through AI-powered career guidance and job matching.
            </p>
          </Col>
          <Col md={2} className="mb-4">
            <h5>Quick Links</h5>
            <ul className="list-unstyled">
              <li><Link to="/" className="text-decoration-none text-light">Home</Link></li>
              <li><Link to="/jobs" className="text-decoration-none text-light">Jobs</Link></li>
              <li><Link to="/login" className="text-decoration-none text-light">Login</Link></li>
              <li><Link to="/register" className="text-decoration-none text-light">Register</Link></li>
            </ul>
          </Col>
          <Col md={3} className="mb-4">
            <h5>Resources</h5>
            <ul className="list-unstyled">
              <li><a href="#" className="text-decoration-none text-light">Career Tips</a></li>
              <li><a href="#" className="text-decoration-none text-light">Resume Builder</a></li>
              <li><a href="#" className="text-decoration-none text-light">Interview Preparation</a></li>
              <li><a href="#" className="text-decoration-none text-light">Industry Insights</a></li>
            </ul>
          </Col>
          <Col md={3} className="mb-4">
            <h5>Contact</h5>
            <ul className="list-unstyled">
              <li><i className="bi bi-geo-alt me-2"></i> Kigali, Rwanda</li>
              <li><i className="bi bi-envelope me-2"></i> info@aicg.rw</li>
              <li><i className="bi bi-telephone me-2"></i> +250 788 123 456</li>
            </ul>
          </Col>
        </Row>
        <hr className="border-light" />
        <Row>
          <Col className="text-center py-3">
            <p>&copy; {new Date().getFullYear()} AI Career Guidance. All rights reserved.</p>
          </Col>
        </Row>
      </Container>
    </footer>
  );
}

export default Footer;