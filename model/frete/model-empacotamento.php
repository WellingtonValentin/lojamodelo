<?php

class ModelEmpacotamento extends MainModel {

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    /**
     * Empacota a encomenda em caixas para a entrega de acordo com os padrões cadastrados pelo cliente
     * 
     * @global type $arrayProdutos
     * @param type $produtos
     * @param type $embalagem
     * @return int
     */
    function empacotar($produtos, $embalagem) {

        global $arrayProdutos;
        $totalProdutos = array_sum($produtos["quantidade"]);
        $arrayProdutos = array();

        /// ARRUMANDO ARRAY VINDO DO POST ///
        $indice1 = 0;
        $indice2 = 0;
        $conta = 0;
        while ($conta < $totalProdutos) {
            if ($produtos["quantidade"][$indice1] > 1) {
                for ($conta2 = 1; $conta2 <= $produtos["quantidade"][$indice1]; $conta2++) {
                    $arrayProdutos[$indice2]["titulo"] = $produtos["titulo"][$indice1];
                    $arrayProdutos[$indice2]["codigoProduto"] = $produtos["codigoProduto"][$indice1];
                    $arrayProdutos[$indice2]["altura"] = $produtos["altura"][$indice1];
                    $arrayProdutos[$indice2]["largura"] = $produtos["largura"][$indice1];
                    $arrayProdutos[$indice2]["profundidade"] = $produtos["profundidade"][$indice1];
                    $arrayProdutos[$indice2]["peso"] = $produtos["peso"][$indice1];
                    $indice2++;
                    $conta++;
                }
            } else {
                $arrayProdutos[$indice2]["titulo"] = $produtos["titulo"][$indice1];
                $arrayProdutos[$indice2]["codigoProduto"] = $produtos["codigoProduto"][$indice1];
                $arrayProdutos[$indice2]["altura"] = $produtos["altura"][$indice1];
                $arrayProdutos[$indice2]["largura"] = $produtos["largura"][$indice1];
                $arrayProdutos[$indice2]["profundidade"] = $produtos["profundidade"][$indice1];
                $arrayProdutos[$indice2]["peso"] = $produtos["peso"][$indice1];
                $indice2++;
                $conta++;
            }
            $indice1++;
        }

        /// DESCOBRINDO PARA QUE LADO VIRAR OS PRODUTOS ///
        if ($embalagem["altura"] < $embalagem["largura"] && $embalagem["altura"] < $embalagem["profundidade"]) {
            $menorLado = "altura";
            if ($embalagem["largura"] < $embalagem["profundidade"]) {
                $segMenorLado = "largura";
                $maiorLado = "profundidade";
            } else {
                $maiorLado = "largura";
                $segMenorLado = "profundidade";
            }
        } elseif ($embalagem["largura"] < $embalagem["profundidade"]) {
            $menorLado = "largura";
            if ($embalagem["altura"] < $embalagem["profundidade"]) {
                $segMenorLado = "altura";
                $maiorLado = "profundidade";
            } else {
                $maiorLado = "altura";
                $segMenorLado = "profundidade";
            }
        } else {
            $menorLado = "profundidade";
            if ($embalagem["altura"] < $embalagem["largura"]) {
                $segMenorLado = "altura";
                $maiorLado = "largura";
            } else {
                $maiorLado = "altura";
                $segMenorLado = "largura";
            }
        }

        ///  VIRANDO AS CAIXAS PARA O MENOR LADO ///
        $produtosArrumados = $this->virarCaixas($arrayProdutos, $menorLado, $segMenorLado, $maiorLado);

        /// COLOCANDO O PRIMEIRO PRODUTO NA EMBALAGEM DE ENTREGA ///
        $menor = $produtosArrumados[0][$menorLado];
        foreach ($produtosArrumados as $ind => $valor) {
            if ($valor[$menorLado] <= $menor) {
                $menor = $valor[$menorLado];
                $indiceMenor = $ind;
            }
        }
        $caixaFinal["0"]["titulo"] = "Caixa 1";
        $caixaFinal["0"]["altura"] = $produtosArrumados[$indiceMenor]["altura"];
        $caixaFinal["0"]["largura"] = $produtosArrumados[$indiceMenor]["largura"];
        $caixaFinal["0"]["profundidade"] = $produtosArrumados[$indiceMenor]["profundidade"];
        $caixaFinal["0"]["peso"] = $produtosArrumados[$indiceMenor]["peso"];
        $produtosArrumados[$indiceMenor]["posicaoZ"] = 1;
        $produtosArrumados[$indiceMenor]["posicaoY"] = 1;
        $produtosArrumados[$indiceMenor]["posicaoX"] = 1;
        $produtosArrumados[$indiceMenor]["posicaoCaixa"] = 1;
        $arrayProdutos = $produtosArrumados;

        $caixaAtual = 0;
        $indiceJaFoi[] = $indiceMenor;
        $envio["pacotes"] = $this->encaixotar(1, 1, 1, $embalagem, $caixaFinal, $caixaAtual, $indiceJaFoi, $totalProdutos - 1);
        $envio["produtos"] = $arrayProdutos;
        return $envio;
    }

    /**
     * Pega produto por produto e vai adicionando em caixas
     * 
     * @global type $arrayProdutos
     * @param type $ultimoX
     * @param type $ultimoY
     * @param type $ultimoZ
     * @param type $embalagem
     * @param type $caixa
     * @param type $caixaAtual
     * @param type $indiceJaFoi
     * @param type $totalProdutos
     * @param int $contaX
     * @param type $contaY
     * @param type $contaZ
     * @param type $contaCaixa
     * @return \type
     */
    function encaixotar($ultimoX, $ultimoY, $ultimoZ, $embalagem, $caixa, $caixaAtual, $indiceJaFoi, $totalProdutos, $contaX = 1, $contaY = 1, $contaZ = 1, $contaCaixa = 1) {
        global $arrayProdutos;
        /// DESCOBRINDO PARA QUE LADO VIRAR OS PRODUTOS ///
        if ($embalagem["altura"] < $embalagem["largura"] && $embalagem["altura"] < $embalagem["profundidade"]) {
            $menorLado = "altura";
            if ($embalagem["largura"] < $embalagem["profundidade"]) {
                $segMenorLado = "largura";
                $maiorLado = "profundidade";
            } else {
                $maiorLado = "largura";
                $segMenorLado = "profundidade";
            }
        } elseif ($embalagem["largura"] < $embalagem["profundidade"]) {
            $menorLado = "largura";
            if ($embalagem["altura"] < $embalagem["profundidade"]) {
                $segMenorLado = "altura";
                $maiorLado = "profundidade";
            } else {
                $maiorLado = "altura";
                $segMenorLado = "profundidade";
            }
        } else {
            $menorLado = "profundidade";
            if ($embalagem["altura"] < $embalagem["largura"]) {
                $segMenorLado = "altura";
                $maiorLado = "largura";
            } else {
                $maiorLado = "altura";
                $segMenorLado = "largura";
            }
        }

        $menor["altura"] = 9999999;
        $menor["largura"] = 9999999;
        $menor["profundidade"] = 9999999;
        foreach ($arrayProdutos as $ind => $valor) {
            if (!in_array($ind, $indiceJaFoi)) {
                if ($valor["profundidade"] < $menor["profundidade"]) {
                    $menor["profundidade"] = $valor["profundidade"];
                    $indiceMenor["profundidade"] = $ind;
                }
                if ($valor["largura"] < $menor["largura"]) {
                    $menor["largura"] = $valor["largura"];
                    $indiceMenor["largura"] = $ind;
                }
                if ($valor["altura"] < $menor["altura"]) {
                    $menor["altura"] = $valor["altura"];
                    $indiceMenor["altura"] = $ind;
                }
            }
        }

        if ($totalProdutos) {
            $totalProfundidade = 0;
            $totalLargura = 0;
            foreach ($arrayProdutos as $key => $value) {
                if (isset($value["posicaoZ"])) {
                    if ($ultimoX == $value["posicaoX"] && $ultimoY == $value["posicaoY"] && $caixaAtual == $value["posicaoCaixa"] - 1) {
                        $totalProfundidade += $value["profundidade"];
                    }
                    if ($ultimoX == $value["posicaoX"] && $ultimoZ == $value["posicaoZ"] && $caixaAtual == $value["posicaoCaixa"] - 1) {
                        $totalLargura += $value["largura"];
                    }
                }
            }
            if ((($caixa[$caixaAtual]["profundidade"] + $menor["profundidade"]) <= $embalagem["profundidade"] OR ( $totalProfundidade + $menor["profundidade"]) <= $embalagem["profundidade"]) && (($caixa[$caixaAtual]["profundidade"] + $menor["profundidade"]) + $caixa[$caixaAtual]["largura"] + $caixa[$caixaAtual]["altura"]) <= 200 && ($arrayProdutos[$indiceMenor["profundidade"]]["peso"] + $caixa[$caixaAtual]["peso"] < 30 )) {
                if (($caixa[$caixaAtual]["profundidade"] + $menor["profundidade"]) <= $embalagem["profundidade"]) {
                    $contaZ++;
                    $caixa[$caixaAtual]["profundidade"] += $menor["profundidade"];
                } else {
                    $contaZ++;
                    if ($caixa[$caixaAtual]["profundidade"] <= ($menor["profundidade"] + $totalProfundidade)) {
                        $caixa[$caixaAtual]["profundidade"] = $menor["profundidade"] + $totalProfundidade;
                    }
                }
                $indiceJaFoi[] = $indiceMenor["profundidade"];
                if ($caixa[$caixaAtual]["altura"] < $arrayProdutos[$indiceMenor["profundidade"]]["altura"]) {
                    $caixa[$caixaAtual]["altura"] = $arrayProdutos[$indiceMenor["profundidade"]]["altura"];
                }
                if ($caixa[$caixaAtual]["largura"] < $arrayProdutos[$indiceMenor["profundidade"]]["largura"]) {
                    $caixa[$caixaAtual]["largura"] = $arrayProdutos[$indiceMenor["profundidade"]]["largura"];
                }
                $caixa[$caixaAtual]["peso"] += $arrayProdutos[$indiceMenor["profundidade"]]["peso"];
                $ultimoZ = $arrayProdutos[$indiceMenor["profundidade"]]["posicaoZ"] = $contaZ;
                $ultimoY = $arrayProdutos[$indiceMenor["profundidade"]]["posicaoY"] = $contaY;
                $ultimoX = $arrayProdutos[$indiceMenor["profundidade"]]["posicaoX"] = $contaX;
                $arrayProdutos[$indiceMenor["profundidade"]]["posicaoCaixa"] = $contaCaixa;
            } elseif (((($caixa[$caixaAtual]["largura"] + $menor["largura"]) <= $embalagem["largura"]) OR ( $totalLargura + $menor["largura"]) <= $embalagem["largura"]) && ($caixa[$caixaAtual]["profundidade"] + ($caixa[$caixaAtual]["largura"] + $menor["largura"]) + $caixa[$caixaAtual]["altura"]) <= 200 && ($arrayProdutos[$indiceMenor["largura"]]["peso"] + $caixa[$caixaAtual]["peso"] < 30 )) {
                $contaZ = 1;
                if (($caixa[$caixaAtual]["largura"] + $menor["largura"]) <= $embalagem["largura"]) {
                    $contaY++;
                    $caixa[$caixaAtual]["largura"] += $menor["largura"];
                } else {
                    $contaY++;
                    if ($caixa[$caixaAtual]["largura"] <= ($menor["largura"] + $totalLargura)) {
                        $caixa[$caixaAtual]["largura"] = $menor["largura"] + $totalLargura;
                    }
                }
                $indiceJaFoi[] = $indiceMenor["largura"];
                if ($caixa[$caixaAtual]["altura"] < $arrayProdutos[$indiceMenor["largura"]]["altura"]) {
                    $caixa[$caixaAtual]["altura"] = $arrayProdutos[$indiceMenor["largura"]]["altura"];
                }
                if ($caixa[$caixaAtual]["profundidade"] < $arrayProdutos[$indiceMenor["largura"]]["profundidade"]) {
                    $caixa[$caixaAtual]["profundidade"] = $arrayProdutos[$indiceMenor["largura"]]["profundidade"];
                }
                $caixa[$caixaAtual]["peso"] += $arrayProdutos[$indiceMenor["largura"]]["peso"];
                $ultimoZ = $arrayProdutos[$indiceMenor["largura"]]["posicaoZ"] = $contaZ;
                $ultimoY = $arrayProdutos[$indiceMenor["largura"]]["posicaoY"] = $contaY;
                $ultimoX = $arrayProdutos[$indiceMenor["largura"]]["posicaoX"] = $contaX;
                $arrayProdutos[$indiceMenor["largura"]]["posicaoCaixa"] = $contaCaixa;
            } elseif (($caixa[$caixaAtual]["altura"] + $menor["altura"]) <= $embalagem["altura"] && ($caixa[$caixaAtual]["profundidade"] + $caixa[$caixaAtual]["largura"] + ($caixa[$caixaAtual]["altura"] + $menor["altura"])) <= 200 && ($arrayProdutos[$indiceMenor["altura"]]["peso"] + $caixa[$caixaAtual]["peso"] < 30 )) {
                $indiceJaFoi[] = $indiceMenor["altura"];
                $caixa[$caixaAtual]["altura"] += $menor["altura"];
                if ($caixa[$caixaAtual]["profundidade"] < $arrayProdutos[$indiceMenor["altura"]]["profundidade"]) {
                    $caixa[$caixaAtual]["profundidade"] = $arrayProdutos[$indiceMenor["altura"]]["profundidade"];
                }
                if ($caixa[$caixaAtual]["largura"] < $arrayProdutos[$indiceMenor["altura"]]["largura"]) {
                    $caixa[$caixaAtual]["largura"] = $arrayProdutos[$indiceMenor["altura"]]["largura"];
                }
                $contaZ = 1;
                $contaY = 1;
                $contaX++;
                $caixa[$caixaAtual]["peso"] += $arrayProdutos[$indiceMenor["altura"]]["peso"];
                $ultimoZ = $arrayProdutos[$indiceMenor["altura"]]["posicaoZ"] = $contaZ;
                $ultimoY = $arrayProdutos[$indiceMenor["altura"]]["posicaoY"] = $contaY;
                $ultimoX = $arrayProdutos[$indiceMenor["altura"]]["posicaoX"] = $contaX;
                $arrayProdutos[$indiceMenor["altura"]]["posicaoCaixa"] = $contaCaixa;
            } else {
                $ultimoZ = $contaZ = 1;
                $ultimoY = $contaY = 1;
                $ultimoX = $contaX = 1;
                $contaCaixa++;
                $caixaAtual++;
                if ($indiceMenor["altura"]) {
                    $indiceMenor2 = $indiceMenor["altura"];
                } elseif ($indiceMenor["largura"]) {
                    $indiceMenor2 = $indiceMenor["largura"];
                } else {
                    $indiceMenor2 = $indiceMenor["profundidade"];
                }
                $indiceJaFoi[] = $indiceMenor2;
                $caixa[$caixaAtual]["titulo"] = "Caixa " . $contaCaixa;
                $caixa[$caixaAtual]["altura"] = $arrayProdutos[$indiceMenor2]["altura"];
                $caixa[$caixaAtual]["largura"] = $arrayProdutos[$indiceMenor2]["largura"];
                $caixa[$caixaAtual]["profundidade"] = $arrayProdutos[$indiceMenor2]["profundidade"];
                if (isset($caixa[$caixaAtual]["peso"])) {
                    $caixa[$caixaAtual]["peso"] += $arrayProdutos[$indiceMenor2]["peso"];
                } else {
                    $caixa[$caixaAtual]["peso"] = $arrayProdutos[$indiceMenor2]["peso"];
                }
                $arrayProdutos[$indiceMenor2]["posicaoX"] = $contaX;
                $arrayProdutos[$indiceMenor2]["posicaoY"] = $contaY;
                $arrayProdutos[$indiceMenor2]["posicaoZ"] = $contaZ;
                $arrayProdutos[$indiceMenor2]["posicaoCaixa"] = $contaCaixa;
            }
            $caixaFinal = $this->encaixotar($ultimoX, $ultimoY, $ultimoZ, $embalagem, $caixa, $caixaAtual, $indiceJaFoi, $totalProdutos - 1, $contaX, $contaY, $contaZ, $contaCaixa);
        } else {
            return $caixa;
        }
        return $caixaFinal;
    }

    /**
     * "Vira" as caixas do produto com o maior lado para cima
     * 
     * @param type $produtos
     * @param type $menorLado
     * @param type $segMenorLado
     * @param type $maiorLado
     * @return type
     */
    function virarCaixas($produtos, $menorLado, $segMenorLado, $maiorLado) {
        foreach ($produtos as $ind => $valor) {
            if ($valor[$menorLado] > $valor[$maiorLado] && $valor[$maiorLado] < $valor[$segMenorLado]) {
                $aux1 = $valor[$menorLado];
                $aux2 = $valor[$maiorLado];
                $produtos[$ind][$menorLado] = $aux2;
                $produtos[$ind][$maiorLado] = $aux1;
            } elseif ($valor[$menorLado] > $valor[$segMenorLado]) {
                $aux1 = $valor[$menorLado];
                $aux2 = $valor[$segMenorLado];
                $produtos[$ind][$menorLado] = $aux2;
                $produtos[$ind][$segMenorLado] = $aux1;
            }
            if ($produtos[$ind][$segMenorLado] > $produtos[$ind][$maiorLado]) {
                $aux3 = $produtos[$ind][$maiorLado];
                $aux4 = $produtos[$ind][$segMenorLado];
                $produtos[$ind][$maiorLado] = $aux4;
                $produtos[$ind][$segMenorLado] = $aux3;
            }
        }
        return $produtos;
    }

    /**
     * Função em especial para empacotamento de envelope
     * 
     * @return type
     */
    function empacotaEnvelope() {

        $envelope = 0;
        $this->db->tabela = "produto_combinacao";
        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
            $combinacao = $this->db->consultaId($produtoCarrinho["COMBINACAO"]);

            if (!isset($envio) || (($combinacao["peso"] + array_sum($envio["ENVELOPE"][$envelope]["PESO"])) >= 0.5)) {
                $envelope++;
                $envio["ENVELOPE"][$envelope]["PESO"] = $combinacao["peso"];
            } else {
                $envio["ENVELOPE"][$envelope]["PESO"] += $combinacao["peso"];
            }
        }
        return $envio;
    }

}