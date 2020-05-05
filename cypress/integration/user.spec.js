describe('User', function() {
  context('Admin', () => {
    it('Show user management', () => {
      cy.login('admin@example.com', 'test123');
      cy.navigate('/admin/users');
    });

    it('Create user', () => {
      cy.login('admin@example.com', 'test123');
      // create new user
      cy.navigate('/admin/user/new');
      cy.get('[data-test="email"]').type('test1@example.com');
      cy.get('[data-test="password"]').type('test123');
      cy.get('[data-test="repeat-password"]').type('test123');
      cy.get('[data-test="submit"]').click();

      cy.logout();
      // attempt login with new user
      cy.login('test1@example.com', 'test123');
      cy.url().should('include', Cypress.env('baseUrl'));
      cy.get('[data-test="user-email"]').contains('test1@example.com');

      cy.logout();
      // login with admin and delete user
      cy.deleteUser('test1@example.com');
    });

    it('Invitation', () => {
      cy.login('admin@example.com', 'test123');
      // invite new user
      cy.navigate('/admin/user/new');
      cy.get('[data-test="email"]').type('test2@example.com');
      cy.get('[data-test="user-verified"]').uncheck();
      cy.get('[data-test="user-invite"]').check();
      cy.get('[data-test="submit"]').click();

      // get invitation link from mailbox
      cy.navigate('/admin/mailbox');

      // attempt to use invitation link
      cy.get('[data-test="mailbox-body"] .cta').invoke('attr', 'href').then(url => {
          url = url.replace(Cypress.env('baseUrl'), '');

          cy.logout();
          cy.navigate(url);

          // set password
          cy.get('[data-test="password"]').type('test123');
          cy.get('[data-test="repeat-password"]').type('test123');
          cy.get('[data-test="submit"]').click();

          // attempt to login with new password
          cy.logout();
          cy.login('test2@example.com', 'test123');
          cy.get('[data-test="user-email"]').contains('test2@example.com');
        }
      );
    });
  });
});
