describe('Profile', function() {
  context('Admin', () => {
    it('Change password', () => {
      cy.login('user4@example.com', 'test123');
      cy.navigate('/#/user/account');

      cy.get('.password-change-form [data-test="current-password"]').type('test123');
      cy.get('.password-change-form [data-test="password"]').type('qwe123');
      cy.get('.password-change-form [data-test="repeat-password"]').type('qwe123');
      cy.get('.password-change-form [data-test="submit"]').should('be.disabled');

      cy.get('.password-change-form [data-test="password"]').type('deface1234');
      cy.get('.password-change-form [data-test="repeat-password"]').type('deface1234');
      cy.get('.password-change-form [data-test="submit"]').should('be.enabled');

      cy.get('.password-change-form [data-test="submit"]').click();

      cy.logout();
      cy.login('user4@example.com', 'deface1234');
      cy.get('[data-test="user-email"]').contains('user4@example.com');
    });
  });
});
