<div class="inputs form large-10 medium-9 columns" ng-controller="CmsInputCtrl">
    
    <shortcut></shortcut>

    <a href="<?php echo $this->Url->build(['action' => 'index']); ?>" class="btn btn-default pull-right">Listar Inputs</a>
    <h2>Adicionar Novo Input</h2>
    <hr>

    <?= $this->Form->create($input) ?>
    <fieldset ng-init='input = <?php echo json_encode($input); ?>; input.type = "registro_textual";'>
        <?php
            echo $this->Form->input('type', ['label' => 'Tipo', 'options' => ['calendario' => 'Calendário', 'intervalo_tempo' => 'Intervalo de Tempo', 'registro_textual' => 'Registro Textual', 'escala_numerica' => 'Escala Numérica', 'escala_texto' => 'Escala de Texto', 'numero' => 'Número', 'texto_privativo' => 'Texto Privativo'], 'class' => 'form-control', 'ng-model' => 'input.type' ]);
            echo $this->Form->input('user_id', ['options' => $users, 'label' => 'Estudante', 'class' => 'form-control', 'type' => 'hidden']);
            echo $this->Form->input('model', ['label' => 'Modelo', 'options' => ['All' => 'Todos', 'Protectors' => 'Responsáveis', 'Schools' => 'Escola', 'Tutors' => 'Tutor', 'Therapists' => 'Terapeuta'], 'class' => 'form-control' ]);
            echo $this->Form->input('name', ['label' => 'Nome', 'class' => 'form-control']);
        ?>

        <div class="config" ng-if="input.type == 'escala_numerica'">
            <?php echo $this->Form->input('config.min', ['label' => 'Valor Mínimo', 'class' => 'form-control', 'ng-model' => 'input.config.min']); ?>
            <?php echo $this->Form->input('config.max', ['label' => 'Valor Máximo', 'class' => 'form-control', 'ng-model' => 'input.config.max']); ?>
        </div>

        <div class="config" ng-if="input.type == 'escala_texto'">

            <strong>Dica: Se você pressionar Ctrl+B, automaticamente irá adicionar mais uma opção</strong>

            <div class="clearfix"></div>

            <a href="javascript:;" class="btn btn-xs btn-info" style="margin-bottom: 20px; margin-top: 20px;" ng-click="adicionar_mais()">Adicionar +</a>

            <div class="clearfix"></div>

            <div class="form-group">
                <textarea class="form-control" name="config[options][{{index}}]" ng-repeat="(index, option) in input.config.options track by $index" placeholder="Preencha com um nome">{{option}}</textarea>
            </div>
        </div>
    </fieldset>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-floppy-o"></i> Salvar</button>
    </div>

    <?= $this->Form->end() ?>
</div>
