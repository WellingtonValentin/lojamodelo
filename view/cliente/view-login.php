<? if (isset($_POST["cadastrarCliente"]) || $this->parametros[0] == "cadastro") { ?>
    <script>
        $(document).ready(function ($) {
            trocaTabIdentificacao("#tab-cadastro");
        });
    </script>
<? } ?>
<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="color-padrao">Identificação</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div id="tab-login" class="tab-identificacao tab-ativa raleway normal size-2 color-dark-gray text-center identificacao-ativa" onclick="trocaTabIdentificacao(this)">
                    JÁ SOU CLIENTE
                </div>
                <div id="tab-cadastro" class="tab-identificacao raleway normal size-2 color-dark-gray text-center" onclick="trocaTabIdentificacao(this)">
                    AINDA NÃO SOU CLIENTE
                </div>
                <div class="tab-conteudo tab-ativa" id="login">
                    <div  class="col-md-6 col-md-offset-3 col-xs-12 col-xs-offset-0">
                        <label class="light size-1-2">Se você já é cadastrado em nosso site digite seus dados aqui.</label>
                        <?
                        if (isset($resultadoLogin)) {
                            ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $resultadoLogin ?>
                            </div>
                            <?
                        }
                        ?>
                        <form action="" method="POST" class="form-login form-horizontal">
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="loginCli" class="control-label size-1-4">E-mail: </label>
                                </div>
                                <div class="col-md-10 input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input type="text" name="email" class="form-control" id="loginCli" placeholder="E-mail">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="senhaCli" class="control-label size-1-4">Senha: </label>
                                </div>
                                <div class="col-md-10 input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input type="password" name="senha" class="form-control" id="senhaCli" placeholder="Senha">
                                </div>
                            </div>
                            <div class="form-group text-left">
                                <div class="col-md-5">
                                    <label for="senhaCli" class="control-label size-1-2 cursor-pointer" data-toggle="modal" data-target="#quadroEsqueciSenha">Esqueci minha senha.</label>
                                </div>
                                <div class="col-md-7 padding-0 text-right">
                                    <button type="submit" name="loginCliente" class="btn btn-info right">
                                        <i class="glyphicon glyphicon-log-in"></i>&nbsp;&nbsp;&nbsp;ENTRAR
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="modal fade bs-example-modal-sm" id="quadroEsqueciSenha" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Esqueci minha senha.</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form class="lembrarSenha">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-10 col-md-offset-1 size-1-2 text-center">
                                                        Informe o seu e-mail que enviaremos um link para que você possa alterar a sua senha.
                                                    </div>
                                                </div><br/>
                                                <div class="row">
                                                    <div class="col-md-10 col-md-offset-1 input-group">
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                        <input type="text" name="email" class="form-control" id="emailEsqueciSenha" placeholder="E-mail">
                                                    </div>
                                                </div><br/>
                                                <div id="resultado-modal-sucesso" class="display-none alert alert-success size-1-2 text-center" role="alert">
                                                    Um link para a mudança de senha foi enviado para o seu e-mail!
                                                </div>
                                                <div id="resultado-modal-erro" class="display-none alert alert-danger size-1-2 text-center" role="alert"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
                                        <button class="btn btn-success" id="submitEsqueciSenha">ENVIAR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-conteudo" id="cadastro">
                    <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0">
                        <label class="light size-1-2">Se você ainda não é cadastrado, informe seus dados abaixo e cadastre-se.</label><br/><br/>
                        <?
                        if (isset($resultadoCadastro)) {
                            ?>
                            <div class="alert alert-<?= ($resultadoCadastro["STATUS"] == "ERRO") ? "danger" : "success" ?>" role="alert">
                                <?= $resultadoCadastro["MSG"] ?>
                            </div>
                            <?
                        }
                        if (chkArray($_POST, "tipo") == "JURIDICA") {
                            ?>
                            <script>
                                $(document).ready(function ($) {
                                    trocaTipo("JURIDICA");
                                });
                            </script>
                            <?
                        } else {
                            ?>
                            <script>
                                $(document).ready(function ($) {
                                    trocaTipo("FISICA");
                                });
                            </script>
                        <? } ?>
                        <form action="" method="POST" class="form-contato size-1-4">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label class="control-label" for="tipo">Tipo de Cadastro</label>
                                    <select class="form-control" id="tipo" name="tipo" onchange="trocaTipo($(this).val())">
                                        <? foreach ($tipoCliente as $ind => $tipo) { ?>
                                            <option <?= (chkArray($_POST, "tipo") == $ind) ? "selected=\"selected\"" : "" ?> value="<?= $ind ?>"><?= $tipo ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-sm-4 <?= (chkArray($_POST, "nome") == "" AND isset($_POST["nome"])) ? "has-error" : "" ?>">
                                    <label class="control-label pf" for="nome">Nome*</label>
                                    <label class="control-label pj" for="nome">Nome Fantasia*</label>
                                    <input type="text" name="nome" value="<?= chkArray($_POST, "nome") ?>" placeholder="Nome*" class="form-control" id="nome"/>
                                </div>
                                <div class="col-sm-5 pf <?= (chkArray($_POST, "sobrenome") == "" AND isset($_POST["sobrenome"])) ? "has-error" : "" ?>">
                                    <label class="control-label pf" for="sobrenome">Sobrenome*</label>
                                    <input type="text" name="sobrenome" value="<?= chkArray($_POST, "sobrenome") ?>" placeholder="Sobrenome*" class="form-control" id="sobrenome"/>
                                </div>
                            </div>
                            <div class="form-group row pj">
                                <div class="col-sm-4">
                                    <label class="control-label" for="responsavel">Responsável</label>
                                    <input type="text" name="responsavel" value="<?= chkArray($_POST, "responsavel") ?>" placeholder="Responsável" class="form-control" id="responsavel"/>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label" for="razao">Razão Social</label>
                                    <input type="text" name="razao" value="<?= chkArray($_POST, "razao") ?>" placeholder="Razão Social" class="form-control" id="razao"/>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label" for="inscricao">Inscrição Estadual</label>
                                    <input type="text" name="inscricao" value="<?= chkArray($_POST, "inscricao") ?>" placeholder="Inscrição Estadual" class="form-control" id="inscricao"/>
                                </div>
                            </div>
                            <div class="form-group row pf">
                                <div class="col-sm-4">
                                    <label class="control-label" for="rg">RG</label>
                                    <input type="text" name="rg" value="<?= chkArray($_POST, "rg") ?>" placeholder="RG" class="form-control" id="rg"/>
                                </div>
                                <div class="col-sm-4 <?= (chkArray($_POST, "documento") == "" AND isset($_POST["documento"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="documento">CPF*</label>
                                    <input type="text" name="documento" value="<?= chkArray($_POST, "documento") ?>" placeholder="CPF*" class="form-control cpf" id="documento"/>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label" for="data_nascimento">Data de Nascimento</label>
                                    <input type="text" name="data_nascimento" value="<?= chkArray($_POST, "data_nascimento") ?>" placeholder="Data de Nascimento" class="form-control date" id="data_nascimento"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4 pj <?= (chkArray($_POST, "documento2") == "" AND isset($_POST["documento2"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="documento2">CNPJ*</label>
                                    <input type="text" name="documento2" value="<?= chkArray($_POST, "documento2") ?>" placeholder="CNPJ*" class="form-control cnpj" id="documento2"/>
                                </div>
                                <div class="col-sm-4 pf">
                                    <label class="control-label" for="sexo">Sexo</label>
                                    <select class="form-control" id="sexo" name="sexo">
                                        <? foreach ($sexo as $ind => $sexo) { ?>
                                            <option <?= (chkArray($_POST, "sexo") == $ind) ? "selected=\"selected\"" : "" ?> value="<?= $ind ?>"><?= $sexo ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-sm-4 <?= (chkArray($_POST, "telefone1") == "" AND isset($_POST["telefone1"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="telefone1">Telefone*</label>
                                    <input type="text" name="telefone1" value="<?= chkArray($_POST, "telefone1") ?>" placeholder="Telefone*" class="form-control telefone" id="telefone1"/>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label" for="telefone2">Celular</label>
                                    <input type="text" name="telefone2" value="<?= chkArray($_POST, "telefone2") ?>" placeholder="Celular" class="form-control telefone" id="telefone2"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 <?= (chkArray($_POST, "cep") == "" AND isset($_POST["cep"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="cep">CEP*</label>
                                    <input type="text" name="cep" value="<?= chkArray($_POST, "cep") ?>" onblur="preencheEndereco($(this).val())" placeholder="CEP*" class="form-control cep" id="cep"/>
                                </div>
                                <div class="col-sm-5 <?= (chkArray($_POST, "endereco") == "" AND isset($_POST["endereco"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="endereco">Endereço*</label>
                                    <input type="text" name="endereco" value="<?= chkArray($_POST, "endereco") ?>" placeholder="Endereço*" class="form-control" id="endereco"/>
                                </div>
                                <div class="col-sm-2 <?= (chkArray($_POST, "numero") == "" AND isset($_POST["numero"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="numero">Número*</label>
                                    <input type="text" name="numero" value="<?= chkArray($_POST, "numero") ?>" placeholder="Número*" class="form-control num6" id="numero"/>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label" for="complemento">Complemento</label>
                                    <input type="text" name="complemento" value="<?= chkArray($_POST, "complemento") ?>" placeholder="Complemento" class="form-control" id="complemento"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4 <?= (chkArray($_POST, "bairro") == "" AND isset($_POST["bairro"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="bairro">Bairro*</label>
                                    <input type="text" name="bairro" value="<?= chkArray($_POST, "bairro") ?>" placeholder="Bairro*" class="form-control" id="bairro"/>
                                </div>
                                <div class="col-sm-4 <?= (chkArray($_POST, "cidade") == "" AND isset($_POST["cidade"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="cidade">Cidade*</label>
                                    <input type="text" name="cidade" value="<?= chkArray($_POST, "cidade") ?>" placeholder="Cidade*" class="form-control" id="cidade"/>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label" for="estado">Estado</label>
                                    <select class="form-control" id="estado" name="estado">
                                        <? foreach ($estado as $ind => $estados) { ?>
                                            <option <?= (chkArray($_POST, "estado") == $estados["uf"]) ? "selected=\"selected\"" : "" ?> value="<?= $estados["uf"] ?>"><?= $estados["titulo"] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 <?= (chkArray($_POST, "email") == "" AND isset($_POST["email"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="email">E-mail*</label>
                                    <input type="text" name="email" value="<?= chkArray($_POST, "email") ?>" placeholder="E-mail*" class="form-control" id="email"/>
                                </div>
                                <div class="col-sm-3 <?= (chkArray($_POST, "senha") == "" AND isset($_POST["senha"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="senha2">Senha*</label>
                                    <input type="password" name="senha" value="<?= chkArray($_POST, "senha") ?>" placeholder="Senha*" class="form-control" id="senha2"/>
                                </div>
                                <div class="col-sm-3 <?= (chkArray($_POST, "senhaConfirma") == "" AND isset($_POST["senhaConfirma"])) ? "has-error" : "" ?>">
                                    <label class="control-label" for="senhaConfirma">Confirmar Senha*</label>
                                    <input type="password" name="senhaConfirma" value="<?= chkArray($_POST, "senhaConfirma") ?>" placeholder="Confirmar Senha*" class="form-control" id="senhaConfirma"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="text-right col-sm-12">
                                    <button type="submit" name="cadastrarCliente" class="btn btn-success">
                                        <i class="glyphicon glyphicon-floppy-disk"></i>&nbsp;&nbsp;&nbsp;CADASTRAR
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

