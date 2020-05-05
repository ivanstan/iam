// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.Commands.add('navigate', url => {
  return cy.visit(Cypress.env('baseUrl') + url);
});

Cypress.Commands.add('login', (email, password) => {
  cy.navigate('/login');
  cy.get('[name="email"]').type(email);
  cy.get('[name="password"]').type(password);
  cy.get('[type="submit"]').click();
});

Cypress.Commands.add('logout', () => {
  cy.get('[data-test="logout"]').click({ force: true });
});

Cypress.Commands.add('deleteUser', email => {
  cy.login('admin@example.com', 'test123');

  cy.navigate('/admin/users');
  cy.get(`[data-test="edit-user-${email}"] .delete-button`).click();

  cy.get('[data-test="yes"]').click();

  cy.wait(1000);
});
