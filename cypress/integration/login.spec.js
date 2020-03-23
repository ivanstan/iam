context('Security', () => {
  beforeEach(() => {
    cy.navigate('/login');
  });

  it('Login form validation works', () => {
    cy.get('[type="submit"]').should('be.disabled');

    cy.get('[name="email"]').type('noop');
    cy.get('[name="password"]').type('noop');
    cy.get('[type="submit"]').should('be.disabled');
  });

  it('Error is shown for invalid credentials', () => {
    cy.get('[name="email"]').type('admin@example.com');
    cy.get('[name="password"]').type('test123');
    cy.get('[type="submit"]').should('be.enabled');
    cy.get('[type="submit"]').click();
  });
});
