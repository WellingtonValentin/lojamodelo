<form action="<?= HOME_URL ?>/produto/busca" method="GET">
    <div class="input-group quadro-form-busca">
        <input type="text" name="busca" value="<?= (chkArray($_POST, "busca")) ? $_POST["busca"] : "" ?>" class="form-control" placeholder="Digite aqui o que você procura">
        <span class="input-group-btn size-1-6">
            <button class="btn btn-default" type="submit">
                <span class="color-white source bold">
                    BUSCAR
                </span>
            </button>
        </span>
    </div>
</form>