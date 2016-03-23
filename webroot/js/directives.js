angular.module('RedePga')


.directive('datepicker', function() {
  return {
    link: function(scope, elem, attrs)
    {
      $(elem).datepicker({
        dateFormat: "dd/mm/yy"
      });
    }
  }
})

.directive('tabs', function() {
  return {
    link: function(scope, elem, attrs)
    {
      $(elem).tabs();
    }
  }
})

.directive('selectAluno', function() {
  return {
    link: function(scope, elem, attrs)
    {
      $(elem).change(function() {

        $(this).parent().submit();
      });
    }
  }
})

.directive('escolherCampo', function() {
  return {
    link: function(scope, element, attrs)
    {

      // evento de mudar o select
      element.bind('change', function() {
        // recupera o type do option selecionado
        var type = element.find(":selected").data('type');
        var config = element.find(":selected").data('config');

        // recupera o indice do registro atual
        var indice = attrs.indice;

        // Troca o type do índice selecionado
        scope.registros[indice].type = type;

        // Se o input tiver config, coloca ela tb
        if(type == "escala_numerica")
        {
          scope.registros[indice].value = config.min;
          scope.registros[indice].config = config;
        } else if(type == "escala_texto") {
          scope.registros[indice].value = config.options[0];
          scope.registros[indice].config = config;
        } else {
            scope.registros[indice].value = null;
        }
        scope.$apply();
      });

    }
  };
})