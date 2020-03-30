describe('User', function() {
  context('Admin', () => {
    beforeEach(() => {
      cy.login('admin@example.com', 'test123');
    });

    it('Show user management', () => {
      cy.navigate('/admin/users');
    });

    it('Create user', () => {
      cy.navigate('/admin/user/new');
      cy.get('[data-test="email"]').type('test@example.com');
      cy.get('[data-test="password"]').type('test123');
      cy.get('[data-test="repeat-password"]').type('test123');
      cy.get('[data-test="submit"]').click();

      cy.logout();
      cy.login('test@example.com', 'test123');
      cy.url().should('include', 'https://127.0.0.1:8000/');
    });
  });
});
