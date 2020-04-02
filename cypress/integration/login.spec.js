describe('Security', function() {
  context('Login Form', () => {
    it('Login form validation', () => {
      cy.navigate('/login');

      // email and password empty
      cy.get('[data-test="submit"]').should('be.disabled');

      // email and password are invalid
      cy.get('[name="email"]').type('noop');
      cy.get('[name="password"]').type('noop');
      cy.get('[data-test="submit"]').should('be.disabled');

      // email is correct password is empty
      cy.get('[name="email"]').clear();
      cy.get('[name="email"]').type('admin@example.com');
      cy.get('[name="password"]').clear();
      cy.get('[data-test="submit"]').should('be.disabled');

      // email and password are correct
      cy.get('[name="email"]').clear();
      cy.get('[name="email"]').type('admin@example.com');
      cy.get('[name="password"]').clear();
      cy.get('[name="password"]').type('noop');
      cy.get('[data-test="submit"]').should('be.enabled');
    });

    it('Login', () => {
      cy.navigate('/login');
      cy.get('[name="email"]').type('admin@example.com');
      cy.get('[name="password"]').type('test123');
      cy.get('[type="submit"]').should('be.enabled');
      cy.get('[type="submit"]').click();
      cy.get('header [data-test="user-email"]').contains('admin@example.com');

      cy.url().should('include', Cypress.env('baseUrl'));
    });

    it('Logout', () => {
      cy.get('[data-test="user-menu"]').click();
      cy.get('[data-test="logout"]').click();
    });
  });

  it('Verification', () => {
    cy.navigate('/login');
    cy.login('admin@example.com', 'test123');

    // assert that admin is not verified and request verification mail
    cy.get('[data-test="verify-notification"]').should('be.visible');
    cy.get('[data-test="verify-notification"] button').click();

    // get invitation link from mailbox
    cy.navigate('/admin/mailbox');

    // attempt to use invitation link
    cy.get('[data-test="mailbox-body"] .btn-primary').invoke('attr', 'href').then(url => {
      url = url.replace(Cypress.env('baseUrl'), '');

      cy.logout();
      cy.navigate(url);

      cy.get('[data-test="verify-notification"]').should('not.exist');
    });
  });

  context('Registration', () => {
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
      cy.get('header [data-test="user-email"]').contains('test@example.com');
    });
  });
});
