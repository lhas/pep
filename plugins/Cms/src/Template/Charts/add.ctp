<div class="page-title red darken-3">
    <h2><i class="material-icons left">add</i> Novo Gráfico</h2>

    <div class="actions">
        <a href="<?php echo $this->Url->build(['action' => 'index']); ?>" class="waves-effect waves-light btn"><i class="material-icons left">keyboard_backspace</i> Voltar</a>
    </div> <!-- .actions -->

    <div class="clearfix"></div>
</div> <!-- .page-title -->

<div id="card-grafico" class="card" ng-controller="NovoGraficoCtrl" data-chart='<?php echo $this->formatarGrafico($chart, $user_id); ?>' data-user_id='<?php echo $user_id; ?>'>
    <div class="card-content">

        <?= $this->Form->create($chart) ?>


        <div class="col s12 l6">

          <?php echo $this->Form->input("name", ["id" =>"chart_name", "ng-model" =>"emptyChart.title.text","label"=>"Título"]) ?>

          <?php echo $this->Form->input("subname", ["id" =>"chart_subname", "ng-model" =>"emptyChart.subtitle.text","label"=>"Sub-título"]) ?>

          <p>
            <input name="format" type="radio" id="mensal" ng-model="emptyChart.format" ng-change="mudouGrafico()" value="mensal" />
            <label for="mensal">Mensal</label>
          </p>

          <p>
            <input name="format" type="radio" id="diario" ng-model="emptyChart.format" ng-change="mudouGrafico()" value="diario" />
            <label for="diario">Diário</label>
          </p>

        <div class="card pink lighten-5">
            <div class="card-content">
                <strong class="card-title">Séries</strong>

                <a href="javascript:;" class="btn red" ng-click="adicionar()">Adicionar Série</a>

                <div class="serie-item pink lighten-4" style="margin-top: 10px;" ng-repeat="(key, value) in emptyChart.series track by $index">

                    <a href="javascript:void(0);" class="btn-deletar-serie" ng-click="deletarSerie(key)"><i class="material-icons">delete_forever</i></a>

                    <label for="">Título</label>
                    <input type="text" name="chart_series[{{key}}][name]" ng-model="emptyChart.series[key].name">

                    <label for="">Cor</label>

                    <select class="browser-default" name="chart_series[{{key}}][color]" ng-model="emptyChart.series[key].color">
                      <option value="#F22613">Vermelho</option>
                      <option value="#DB0A5B">Rosa</option>
                      <option value="#446CB3">Azul Escuro</option>
                      <option value="#19B5FE">Azul Claro</option>
                      <option value="#8E44AD">Roxo</option>
                      <option value="#87D37C">Verde Oliva</option>
                      <option value="#26A65B">Verde Eucalipto</option>
                      <option value="#1E824C">Verde Escuro</option>
                      <option value="#F89406">Laranja</option>
                      <option value="#F2784B">Salmão</option>
                      <option value="#F9BF3B">Amarelo</option>
                      <option value="#6C7A89">Cinza Escuro</option>
                      <option value="#D2D7D3">Cinza Claro</option>
                    </select>

                    <label for="">Tipo</label>

                    <select name="chart_series[{{key}}][type]" ng-model="emptyChart.series[key].type" class="browser-default">
                      <?php foreach($types as $k => $v) : ?>
                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                      <?php endforeach; ?>
                    </select>

                    <label for="">Input</label>
                    <select name="chart_series[{{key}}][input_id]" ng-model="emptyChart.series[key].input_id" ng-change="trocou(key)" class="browser-default">
                        <option value="">Selecionar</option>
                        <option ng-repeat="input in inputs" value="{{input.id}}">{{input.name}}</option>
                    </select>

                    <div ng-show="emptyChart.series[key].input_id">

                      <label for="">Matéria</label>
                      <select name="chart_series[{{key}}][theme_id]" ng-model="emptyChart.series[key].theme_id" ng-change="trocou(key)" class="browser-default">
                          <option value="">Todas</option>
                          <option ng-repeat="materia in materias" value="{{materia.id}}">{{materia.name}}</option>
                      </select>

                    </div>

                </div>
            </div>
        </div>

        <button class="btn btn-primary btn-block"><i class="fa fa-floppy-o"></i> Salvar</button>

        </div> <!-- .col -->

        <div id="demonstracao" class="col s12 l6">
            <p><strong>Demonstração:</strong></p>

            <highchart id="grafico_demonstracao" config="emptyChart"></highchart>
        </div> <!-- #demonstracao -->

        <?php echo $this->Form->end(); ?>

        <div class="clearfix"></div>

    </div> <!-- .card-content -->

</div> <!-- .card -->
