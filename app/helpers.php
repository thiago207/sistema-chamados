<?php

if (! function_exists('asset_v')) {
    /**
     * Como asset(), mas acrescenta a data de modificação do arquivo como
     * versão (?v=...). Evita que o navegador sirva um CSS/JS antigo do
     * cache depois que o arquivo muda — sem isso, uma correção no JS só
     * aparece pro usuário depois de um hard refresh manual.
     */
    function asset_v(string $path): string
    {
        $caminhoCompleto = public_path($path);
        $versao = file_exists($caminhoCompleto) ? filemtime($caminhoCompleto) : time();

        return asset($path).'?v='.$versao;
    }
}
