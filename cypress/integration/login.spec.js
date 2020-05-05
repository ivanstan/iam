describe('Security', function() {
  context('Login', () => {
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
      cy.get('[data-test="user-email"]').contains('admin@example.com');

      cy.url().should('include', Cypress.env('baseUrl'));
    });

    it('Logout', () => {
      cy.get('[data-test="user-menu"]').click();
      cy.get('[data-test="logout"]').click();
    });
  });

  it('Registration and verification', () => {
    // register new user
    cy.navigate('/register');
    cy.get('[data-test="email"]').type('test@example.com');
    cy.get('[data-test="password"]').type('test123');
    cy.get('[data-test="repeat-password"]').type('test123');
    cy.get('[data-test="submit"]').click();

    // login with new user
    cy.navigate('/login');
    cy.get('[name="email"]').type('test@example.com');
    cy.get('[name="password"]').type('test123');
    cy.get('[data-test="submit"]').click();
    cy.get('[data-test="user-email"]').contains('test@example.com');

    // request verification mail
    cy.get('[data-test="verify-notification"]').should('be.visible');
    cy.get('[data-test="verify-notification"] button').click();

    // login with administrator
    cy.logout();
    cy.login('admin@example.com', 'test123');

    // get invitation link from mailbox
    cy.navigate('/admin/mailbox');

    cy.get('[data-test="mailbox-body"] .cta')
      .invoke('attr', 'href')
      .then(url => {
        // attempt to use invitation link
        url = url.replace(Cypress.env('baseUrl'), '');

        cy.logout();
        cy.navigate(url);

        cy.get('[data-test="verify-notification"]').should('not.exist');

        cy.logout();
        cy.deleteUser('test@example.com');
      });
  });

  it('Recover password', () => {
    cy.navigate('/recovery');
    cy.get('[data-test="email"]').type('user3@example.com');
    cy.get('[data-test="submit"]').click();

    // login with administrator
    cy.login('admin@example.com', 'test123');

    // get password reset link from mailbox
    cy.navigate('/admin/mailbox');

    cy.get('[data-test="mailbox-body"] .cta')
      .invoke('attr', 'href')
      .then(url => {
        // attempt to use invitation link
        url = url.replace(Cypress.env('baseUrl'), '');

        cy.logout();
        cy.navigate(url);

        cy.get('[data-test="password"]').type('qwe123');
        cy.get('[data-test="repeat-password"]').type('qwe123');
        cy.get('[data-test="submit"]');
        cy.wait(2000);
        cy.navigate('/');
        cy.wait(2000);
        cy.logout();
        cy.login('user3@example.com', 'qwe123');
        cy.get('[data-test="user-email"]').contains('user3@example.com');
        cy.get('[data-test="verify-notification"]').should('not.exist');
      });
  });

  it('Inactive user can\'t login', () => {
    cy.login('user1@example.com', 'test123');
    cy.get('[data-test="alert-danger"]').should('be.visible');
  });
});
