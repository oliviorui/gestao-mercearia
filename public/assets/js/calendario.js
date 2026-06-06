$(document).ready(function () {
  let dataAtual = new Date();

  function formatarMesAno(data) {
    let formato = new Intl.DateTimeFormat("pt-BR", { month: "long", year: "numeric" }).format(data);
    return formato.charAt(0).toUpperCase() + formato.slice(1); // Capitaliza a primeira letra
  }

  function renderizarCalendario() {
    let mes = dataAtual.getMonth();
    let ano = dataAtual.getFullYear();
    let hoje = new Date();
    
    $("#mesAno").text(formatarMesAno(dataAtual));

    let primeiroDia = new Date(ano, mes, 1).getDay();
    let totalDias = new Date(ano, mes + 1, 0).getDate();

    $("#dias").empty();

    // Adiciona espaços vazios antes do primeiro dia do mês
    for (let i = 0; i < primeiroDia; i++) {
      $("#dias").append("<div></div>");
    }

    // Adiciona os dias do mês e destaca a data atual
    for (let dia = 1; dia <= totalDias; dia++) {
      let classe = (hoje.getDate() === dia && hoje.getMonth() === mes && hoje.getFullYear() === ano) ? "hoje" : "";
      $("#dias").append(`<div class="${classe}">${dia}</div>`);
    }
  }

  $("#prev").click(function () {
    dataAtual.setMonth(dataAtual.getMonth() - 1);
    renderizarCalendario();
  });

  $("#next").click(function () {
    dataAtual.setMonth(dataAtual.getMonth() + 1);
    renderizarCalendario();
  });

  renderizarCalendario();
});
