describe('Security', function() {
  context('Login Form', () => {
    it('Login form validation works', () => {
      cy.navigate('/login');
      cy.get('[type="submit"]').should('be.disabled');
      cy.get('[name="email"]').type('noop');
      cy.get('[name="password"]').type('noop');
      cy.get('[type="submit"]').should('be.disabled');
      cy.get('[name="email"]').clear();
      cy.get('[name="email"]').type('admin@example.com');
      // cy.get('[type="submit"]').should('be.disabled');
    });

    it('Login works', () => {
      cy.navigate('/login');
      cy.get('[name="email"]').type('admin@example.com');
      cy.get('[name="password"]').type('test123');
      cy.get('[type="submit"]').should('be.enabled');
      cy.get('[type="submit"]').click();

      cy.url().should('include', 'https://127.0.0.1:8000/');
    });

    it('Logout works', () => {
      cy.get('#user-menu').click();
      cy.get('.logout').click();
    });
  });

  context('Registration form', () => {
    it('Registration works', () => {
      cy.navigate('/register');
      cy.get('[data-test="email"]').type('test@example.com');
      cy.get('[data-test="password"]').type('test123');
      cy.get('[data-test="repeat-password"]').type('test123');
      cy.get('[data-test="submit"]').click();

      cy.navigate('/login');
      cy.get('[name="email"]').type('test@example.com');
      cy.get('[name="password"]').type('test123');
      cy.get('[data-test="submit"]').click();
    });
  });
});
