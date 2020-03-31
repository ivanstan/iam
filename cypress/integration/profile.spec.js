describe('Profile', function() {
  context('Admin', () => {
    it('Change password works', () => {
      cy.login('test@example.com', 'test123');
      cy.navigate('/user/account');

      cy.get('[data-test="current-password"]').type('test123');
      cy.get('[data-test="password"]').type('qwe123');
      cy.get('[data-test="repeat-password"]').type('qwe123');
      cy.get('[data-test="submit"]').click();

      cy.logout();
      cy.login('test@example.com', 'qwe123');
      cy.url().should('include', Cypress.env('baseUrl'));
    });
  });
});
