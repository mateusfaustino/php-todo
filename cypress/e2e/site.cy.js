describe('Teste de teste do estacio', () => {
    it('testa se consegue acessar a pagina da estacio', () => {
        cy.visit('https://www.serasa.com.br/limpa-nome-online/');

        cy.get('#navbar').contains('Serasa Limpa Nome');

        cy.contains('Consultar CPF grátis').click();

        cy.wait(100);

        cy.contains('Digite seu CPF');

        cy.get('[name="cpf"]').type('065.017.513-14');

        cy.contains('Continuar').click();
    })
});