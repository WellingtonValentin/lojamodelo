<section id="conteudo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="color-light-red">Alterar Senha</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 borda-padrao-2">
                <br/><br/>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <? if ($resultado == "OK") { ?>
                            <div class="alert alert-success" role="alert">
                                Senha alterada com sucesso!
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <META HTTP-EQUIV="Refresh" CONTENT="2; URL=<?= HOME_URL ?>/cliente/login/login.html">
                        <? } elseif ($resultado != "") { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $resultado ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        <? } ?>
                        <label class="light">Informe sua nova senha e confirme logo abaixo.</label>
                        <form action="" method="POST" class="form-login form-horizontal">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="senhaCli" class="control-label">Senha: </label>
                                </div>
                                <div class="col-md-8 input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input type="password" name="senha" class="form-control" id="senhaCli" placeholder="Senha">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="confirmar_senha" class="control-label">Confirmar Senha: </label>
                                </div>
                                <div class="col-md-8 input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input type="password" name="confirmar_senha" class="form-control" id="confirmar_senha" placeholder="Confirmar Senha">
                                </div>
                            </div>
                            <div class="form-group text-left">
                                <div class="col-md-12 padding-0 text-right">
                                    <button type="submit" name="alterar_senha" class="btn btn-success right">
                                        <i class="glyphicon glyphicon-log-in"></i>
                                        ALTERAR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>