describe('User', function() {
  context('Admin', () => {
    beforeEach(() => {
      cy.login('admin@example.com', 'test123');
    });

    it('Show user management', () => {
      cy.navigate('/admin/users');
    });

    it('Create user works', () => {
      // create new user
      cy.navigate('/admin/user/new');
      cy.get('[data-test="email"]').type('test1@example.com');
      cy.get('[data-test="password"]').type('test123');
      cy.get('[data-test="repeat-password"]').type('test123');
      cy.get('[data-test="submit"]').click();

      cy.logout();
      // attempt login with new user
      cy.navigate('/login');
      cy.login('test1@example.com', 'test123');
      cy.url().should('include', Cypress.env('baseUrl'));
      cy.get('header [data-test="user-email"]').contains('test1@example.com');

      cy.logout();
      // login with admin and delete user
      cy.navigate('/login');
      cy.login('admin@example.com', 'test123');
      cy.navigate('/admin/users');
      cy.get('[data-user="edit-user-test1@example.com"] .delete').click();
      cy.type('{enter}');
    });

    it('Invitation works', () => {
      // invite new user
      cy.navigate('/admin/user/new');
      cy.get('[data-test="email"]').type('test2@example.com');
      cy.get('[data-test="user-verified"]').uncheck();
      cy.get('[data-test="user-invite"]').check();
      cy.get('[data-test="submit"]').click();

      // get invitation link from mailbox
      cy.navigate('/admin/mailbox');

      // attempt to use invitation link
      cy.get('[data-test="mailbox-body"] .btn-primary').invoke('attr', 'href').then(url => {
          url = url.replace(Cypress.env('baseUrl'), '');

          cy.logout();
          cy.navigate(url);

          // set password
          cy.get('[data-test="password"]').type('test123');
          cy.get('[data-test="repeat-password"]').type('test123');
          cy.get('[data-test="submit"]').click();

          // attempt to login with new password
          cy.logout();
          cy.navigate('/login');
          cy.login('test2@example.com', 'test123');
          cy.get('header [data-test="user-email"]').contains('test2@example.com');
        }
      );
    });
  });
});
