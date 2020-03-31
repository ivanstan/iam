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
      cy.login('test1@example.com', 'test123');
      cy.url().should('include', Cypress.env('baseUrl'));
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
      const invite = cy.get('[data-test="mailbox-body"] .btn-primary').invoke('attr', 'href');

      console.log(invite);


      // attempt to use invitation link
      cy.logout();
      cy.navigate(invite);
    });
  });
});
