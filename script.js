$(document).ready(() => {
	$('#documentacao').on('click',() =>{
        $('#pagina').load('documentacao.html')
    })
	$('#suporte').on('click',() =>{
        $('#pagina').load('suporte.html')
    })

    //ajax
    $('#competencia').on('change', e => {
        let competencia = $(e.target).val()
        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: dados => {
                $('#numeroVendas').html(dados.numeroVendas)
                $('#totalVendas').html(dados.totalVendas)
                $('#clientesAtivos').html(dados.clientesAtivos)
                $('#clientesInativos').html(dados.clientesInativos)
                $('#totalReclamacoes').html(dados.totalDeReclamacoes)
                $('#totalElogios').html(dados.totalDeElogios)
                $('#totalSugestoes').html(dados.totalDeSugestoes)
                $('#totalDespesas').html(dados.totalDeDespesas)
            },
            error: err => {console.log(err)}       
        })
    })
})