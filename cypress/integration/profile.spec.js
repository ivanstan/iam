describe('Profile', function() {
  context('Admin', () => {
    it('Change password', () => {
      cy.login('user4@example.com', 'test123');
      cy.navigate('/#/user/account');

      cy.get('[data-test="current-password"]').type('test123');
      cy.get('[data-test="password"]').type('qwe123');
      cy.get('[data-test="repeat-password"]').type('qwe123');
      cy.get('[data-test="submit"]').click();

      cy.logout();
      cy.login('user4@example.com', 'qwe123');
      cy.get('[data-test="user-email"]').contains('user4@example.com');
    });
  });
});
