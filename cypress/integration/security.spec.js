context('Security', () => {
  //   it('Ban Ip', () => {
  //     for (let i = 0; i <= 6; i++) {
  //       cy.login('user5@example.com', 'noop');
  //       cy.wait(500);
  //     }
  //   });

  it('Registration feature flag', () => {
    cy.navigate('/login');
    cy.get('[data-test="register-link"]').should('be.visible');

    cy.request('/register').then(
      response =>
        function() {
          expect(response.status).to.eq(200);
        }
    );

    // disable registration feature
    cy.login('admin@example.com', 'test123');
    cy.navigate('/admin/settings');

    cy.get('[data-test="feature-registration"]').should('be.checked');
    cy.get('[data-test="feature-registration"]').uncheck();
    cy.get('[data-test="submit"]').click();

    cy.logout();

    cy.get('[data-test="register-link"]').should('not.exist');

    // cy.request('/register', { failOnStatusCode: false }).then(
    //   response =>
    //     function() {
    //       expect(response.status).to.eq(404);
    //     }
    // );

    // enable registration feature
    cy.login('admin@example.com', 'test123');
    cy.navigate('/admin/settings');

    cy.get('[data-test="feature-registration"]').check();
    cy.get('[data-test="submit"]').click();
    cy.logout();
  });
});
