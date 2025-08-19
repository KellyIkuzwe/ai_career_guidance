import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Navbar as BsNavbar, Nav, Container, Button } from 'react-bootstrap';
import { useAuth } from '../../context/AuthContext';

function Navbar() {
  const { currentUser, logout, isJobSeeker, isCompany } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    try {
      await logout();
      navigate('/');
    } catch (error) {
      console.error('Logout failed', error);
    }
  };

  return (
    <BsNavbar bg="white" expand="lg" className="navbar">
      <Container>
        <BsNavbar.Brand as={Link} to="/">
          <img
            src="/images/photo1754988991.jpg"
            height="30"
            className="d-inline-block align-top me-2"
            alt="AI Career Guidance Logo"
          />
          AI Career Guidance
        </BsNavbar.Brand>
        <BsNavbar.Toggle aria-controls="basic-navbar-nav" />
        <BsNavbar.Collapse id="basic-navbar-nav">
          <Nav className="me-auto">
            <Nav.Link as={Link} to="/">Home</Nav.Link>
            <Nav.Link as={Link} to="/jobs">Jobs</Nav.Link>
            {isJobSeeker && (
              <>
                <Nav.Link as={Link} to="/dashboard">Dashboard</Nav.Link>
                <Nav.Link as={Link} to="/ai-matches">AI Matches</Nav.Link>
                <Nav.Link as={Link} to="/my-applications">My Applications</Nav.Link>
              </>
            )}
            {isCompany && (
              <>
                <Nav.Link as={Link} to="/company/dashboard">Dashboard</Nav.Link>
                <Nav.Link as={Link} to="/company/manage-jobs">Manage Jobs</Nav.Link>
                <Nav.Link as={Link} to="/company/post-job">Post Job</Nav.Link>
              </>
            )}
          </Nav>
          <Nav>
            {currentUser ? (
              <>
                <Nav.Link as={Link} to={isCompany ? '/company/dashboard' : '/profile'}>
                  <i className="bi bi-person-circle me-1"></i>
                  {currentUser.name}
                </Nav.Link>
                <Button variant="outline-danger" onClick={handleLogout}>
                  Logout
                </Button>
              </>
            ) : (
              <>
                <Nav.Link as={Link} to="/login">Login</Nav.Link>
                <Nav.Link as={Link} to="/register">
                  <Button variant="primary">Register</Button>
                </Nav.Link>
              </>
            )}
          </Nav>
        </BsNavbar.Collapse>
      </Container>
    </BsNavbar>
  );
}

export default Navbar;