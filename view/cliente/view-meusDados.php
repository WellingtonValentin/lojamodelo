<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9 borda-padrao size-1-2">
                <h2>Meus Dados</h2>
                <? if ($login->userdata["tipo"] == "JURIDICA") { ?>
                    <script>
                        $(document).ready(function ($) {
                            trocaTipo("JURIDICA");
                        });
                    </script>
                <? } else { ?>
                    <script>
                        $(document).ready(function ($) {
                            trocaTipo("FISICA");
                        });
                    </script>
                <? } ?>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo" class="control-label">Tipo de Cadastro: </label>
                                <input type="text" value="<?= $tipoCliente2[$login->userdata["tipo"]] ?>" class="form-control" id="email" placeholder="E-mail" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome" class="control-label pf">Nome: </label>
                                <label for="nome" class="control-label pj">Nome Fantasia: </label>
                                <input type="text" name="nome" value="<?= $login->userdata["nome"] ?>" class="form-control" id="nome" placeholder="Nome">
                            </div>
                        </div>
                        <div class="col-md-6 pf">
                            <div class="form-group">
                                <label for="sobrenome" class="control-label">Sobrenome: </label>
                                <input type="text" name="sobrenome" value="<?= $login->userdata["sobrenome"] ?>" class="form-control" id="sobrenome" placeholder="Sobrenome">
                            </div>
                        </div>
                    </div>
                    <div class="row pj">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsavel" class="control-label">Responsável: </label>
                                <input type="text" name="responsavel" value="<?= $login->userdata["responsavel"] ?>" class="form-control" id="responsavel" placeholder="Responsável">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inscricao" class="control-label">Inscrição Estadual: </label>
                                <input type="text" name="inscricao" value="<?= $login->userdata["inscricao"] ?>" class="form-control" id="inscricao" placeholder="Inscrição Estadual">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 pf">
                            <div class="form-group">
                                <label for="rg" class="control-label">RG: </label>
                                <input type="text" name="rg" value="<?= $login->userdata["rg"] ?>" class="form-control" id="rg" placeholder="RG">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="documento" class="control-label pf">CPF: </label>
                                <label for="documento" class="control-label pj">CNPJ: </label>
                                <input type="text" value="<?= $login->userdata["documento"] ?>" class="form-control" id="documento" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 pf">
                            <div class="form-group">
                                <label for="data_nascimento" class="control-label">Data de Nascimento: </label>
                                <input type="text" value="<?= dataSite($login->userdata["data_nascimento"]) ?>" class="form-control date" id="data_nascimento" disabled placeholder="Data de Nascimento">
                            </div>
                        </div>
                        <div class="col-md-3 pf">
                            <div class="form-group">
                                <label class="control-label" for="sexo">Sexo</label>
                                <select class="form-control" id="sexo" name="sexo">
                                    <? foreach ($sexo as $ind => $sexo) { ?>
                                        <option <?= ($login->userdata["sexo"] == $ind) ? "selected=\"selected\"" : "" ?> value="<?= $ind ?>"><?= $sexo ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9 pj">
                            <div class="form-group">
                                <label for="razao" class="control-label">Razão Social: </label>
                                <input type="text" name="razao" value="<?= $login->userdata["razao"] ?>" class="form-control" id="razao" placeholder="Razão Social">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telefone1" class="control-label">Telefone: </label>
                                <input type="text" name="telefone1" value="<?= $login->userdata["telefone1"] ?>" class="form-control telefone" id="telefone1" placeholder="Telefone">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telefone2" class="control-label">Celular: </label>
                                <input type="text" name="telefone2" value="<?= $login->userdata["telefone2"] ?>" class="form-control telefone" id="telefone2" placeholder="Celular">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">E-mail: </label>
                                <input type="text" value="<?= $login->userdata["email"] ?>" class="form-control" id="email" placeholder="E-mail" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-right">
                                <button type="submit" name="meusDados" class="btn btn-success">
                                    <i class="glyphicon glyphicon-floppy-disk"></i>&nbsp;&nbsp;&nbsp;SALVAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <br/><br/><br/>
                <h2>Endereço Principal</h2>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cep" class="control-label">CEP: </label>
                                <input type="text" name="cep" value="<?= $login->userdata["cep"] ?>" onblur="preencheEndereco($(this).val())" class="form-control cep" id="cep" placeholder="CEP">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="endereco" class="control-label">Endereço: </label>
                                <input type="text" name="endereco" value="<?= $login->userdata["endereco"] ?>" class="form-control" id="endereco" placeholder="Endereço">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numero" class="control-label">Número: </label>
                                <input type="text" name="numero" value="<?= $login->userdata["numero"] ?>" class="form-control num6" id="numero" placeholder="Número">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="complemento" class="control-label">Complemento: </label>
                                <input type="text" name="complemento" value="<?= $login->userdata["complemento"] ?>" class="form-control" id="complemento" placeholder="Complemento">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="bairro" class="control-label">Bairro: </label>
                                <input type="text" name="bairro" value="<?= $login->userdata["bairro"] ?>" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="cidade" class="control-label">Cidade: </label>
                                <input type="text" name="cidade" value="<?= $login->userdata["cidade"] ?>" class="form-control" id="cidade" placeholder="Cidade">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estado" class="control-label">Estado: </label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="">Selecione..</option>
                                    <? foreach ($estado as $ind => $estados) { ?>
                                        <option <?= ($login->userdata["estado"] == $estados["uf"]) ? "selected=\"selected\"" : "" ?> value="<?= $estados["uf"] ?>"><?= $estados["uf"] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-right">
                                <button type="submit" name="enderecoPrincipal" class="btn btn-success">
                                    <i class="glyphicon glyphicon-floppy-disk"></i>&nbsp;&nbsp;&nbsp;SALVAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <br/><br/><br/>
                <h2>Alterar Senha</h2>
                <? if (isset($erro)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $erro ?>
                    </div>
                <? } ?>
                <? if (isset($sucesso)) { ?>
                    <div class="alert alert-success" role="alert">
                        <?= $sucesso ?>
                    </div>
                <? } ?>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="senhaAtual" class="control-label">Senha Atual: </label>
                                <input type="password" name="senhaAtual" class="form-control" id="senhaAtual" placeholder="Senha Atual">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="senhaNova" class="control-label">Senha Nova: </label>
                                <input type="password" name="senhaNova" class="form-control" id="senhaNova" placeholder="Senha Nova">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="senhaConfirma" class="control-label">Confirmar Senha: </label>
                                <input type="password" name="senhaConfirma" class="form-control" id="senhaConfirma" placeholder="Confirmar Senha">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-12 text-left botao-carrinho">
                            <? if (isset($_SESSION['PEDIDO']['CARRINHO'])) { ?>
                                <a href="<?= HOME_URL ?>/carrinho/finalizar/finalizar-compra.html" class="btn btn-block btn-primary btn-success">FINALIZAR COMPRA <span class="glyphicon glyphicon-chevron-right"></span></a>
                            <? } ?>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group text-right">
                                <button type="submit" name="alterarSenha" class="btn btn-success">
                                    <i class="glyphicon glyphicon-floppy-disk"></i>&nbsp;&nbsp;&nbsp;SALVAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>