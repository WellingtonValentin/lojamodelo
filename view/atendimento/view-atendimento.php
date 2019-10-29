<section id="conteudo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="color-padrao">Fale Conosco</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <?
                if ($resultadoContato) {
                    if ($resultadoContato) {
                        ?>
                        <div class="alert alert-success text-center" role="alert">
                            Contato enviado com sucesso!
                        </div>
                    <? } else { ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <strong>Atenção!</strong> Preencha os campos obrigatórios.
                        </div>
                        <?
                    }
                }
                ?>
                <form action="<?= HOME_URL ?>/atendimento/contato/fale-conosco.html" method="POST" class="form-contato">
                    <div class="form-group col-md-6 <?= (isset($_POST["nome"])) ? (!chkArray($_POST, "nome")) ? "has-error" : "" : "" ?>">
                        <label class="control-label" for="nome">Nome</label>
                        <input type="text" name="nome" value="<?= chkArray($_POST, "nome") ?>" placeholder="Nome*" class="form-control" id="nome"/>
                    </div>
                    <div class="form-group col-md-6 <?= (isset($_POST["email"])) ? (!chkArray($_POST, "email")) ? "has-error" : "" : "" ?>">
                        <label class="control-label" for="email">E-mail</label>
                        <input type="email" name="email" value="<?= chkArray($_POST, "email") ?>" placeholder="E-mail*" class="form-control" id="email"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="telefone1">Telefone</label>
                        <input type="text" name="telefone1" value="<?= chkArray($_POST, "telefone1") ?>" placeholder="Telefone" class="form-control telefone" id="telefone1"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="telefone2">Celular</label>
                        <input type="text" name="telefone2" value="<?= chkArray($_POST, "telefone2") ?>" placeholder="Celular" class="form-control telefone" id="telefone2"/>
                    </div>
                    <div class="form-group col-md-12 <?= (isset($_POST["texto"])) ? (!chkArray($_POST, "texto")) ? "has-error" : "" : "" ?>">
                        <label class="control-label" for="texto">Mensagem</label>
                        <textarea name="texto" placeholder="Mensagem*" id="texto" class="form-control" rows="3"><?= chkArray($_POST, "texto") ?></textarea>
                    </div>
                    <div class="form-group col-md-12 text-right">
                        <button type="submit" name="enviaContato" class="btn btn-primary">
                            <i class="glyphicon glyphicon-envelope"></i>
                            ENVIAR
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-sm-6">
                <address class="col-md-6">
                    <strong><?= chkArray($empresa, "titulo") ?>.</strong><br>
                    <?= chkArray($empresa, "endereco") ?><?= chkArray($empresa, "numero") ? ", " . $empresa["numero"] : "" ?><?= ($empresa["complemento"]) ? ", " . $empresa["complemento"] : "" ?><br>
                    <?= chkArray($empresa, "bairro") ?><?= ($empresa["cep"]) ? " - CEP: " . $empresa["cep"] : "" ?><br>
                    <?= chkArray($empresa, "cidade") ?><?= ($empresa["uf"]) ? " - " . $empresa["uf"] : "" ?><br>
                    <?
                    foreach ($telefone as $telefones) {
                        ?>
                        <strong><?= $telefones["titulo"] ?>: </strong><?= $telefones["telefone"] ?><?= ($telefones["ramal"]) ? " - Ramal: " . $telefones["ramal"] : "" ?><br/>
                        <?
                    }
                    ?>
                </address>
                <?
                foreach ($emailContato as $email) {
                    ?>
                    <address class="col-md-6">
                        <strong><?= $email["titulo"] ?></strong><br>
                        <a href="mailto:<?= $email["email"] ?>"><?= $email["email"] ?></a>
                    </address>
                    <?
                }
                $endereco = $empresa["endereco"] . "," . $empresa["numero"] . " " . $empresa["cidade"] . "/" . $empresa["uf"];
                ?>
                <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
                <script>
                    $(document).ready(function () {
                        carregarLocalizacao('<?= $endereco ?>', '<?= $empresa["titulo"] ?>', '<?= $endereco ?>', 'mapa-end');
                    });
                </script>
                <div class="col-md-12">
                    <div id="mapa-end"></div>
                </div>
            </div>
        </div>
    </div>
</section>

