<?php

$clientes = [];
$contas   = [];

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) !== 11) return false;
    
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cpf[9] != $digito1) return false;
    
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    
    return $cpf[10] == $digito2;
}

function menu(){
    global $clientes, $contas;

    print "Bem-vindo ao sistema bancário! \n";
    $nome_user = readline("Me informe qual o seu nome: ");
    $nome_user = strtoupper($nome_user);
    $cpf_user = readline("Me informe seu CPF: ");
    
    if (!validarCPF($cpf_user)) {
        print "CPF inválido!\n";
        return;
    }
    
    if (!clienteExistente($clientes, $cpf_user)) {
        $telefone = readline("Me informe seu número de telefone: ");
        cadastrarCliente($clientes, $nome_user, $cpf_user, $telefone);
        print "Cliente cadastrado com sucesso! \n";
    } else {
        print "Cliente já existe! \n";
    }

    $numeroConta = cadastrarConta($contas, $cpf_user);
    print "Conta criada com sucesso! Número da conta: {$numeroConta}\n";
    
    while (true) {
        print "\nSelecione uma opção: \n";
        print "1. Depositar \n";
        print "2. Sacar \n";
        print "3. Consultar Saldo \n";
        print "4. Sair \n";
        
        $opcao = readline("Escolha uma opção: ");
        
        switch ($opcao) {
            case 1:
                $quantia = (float) readline("Informe o valor do depósito: ");
                depositar($contas, $numeroConta, $quantia);
                break;
            case 2:
                $quantia = (float) readline("Informe o valor do saque: ");
                sacar($contas, $numeroConta, $quantia);
                break;
            case 3:
                consultarSaldo($contas, $numeroConta);
                break;
            case 4:
                print "Saindo do sistema. Até logo! \n";
                return;
            default:
                print "Opção inválida! \n";
        }
    }
}

function clienteExistente($clientes, $cpf) {
    foreach ($clientes as $cliente) {
        if ($cliente['cpf'] == $cpf) {
            return true;
        }
    }
    return false;
}

function cadastrarCliente(&$clientes, string $nome, string $cpf, string $telefone): void {
    $cliente = [
        "nome" => $nome,
        "cpf"  => $cpf,
        "telefone" => $telefone
    ];
    
    $clientes[] = $cliente;
}

function cadastrarConta(&$contas, $cpfCliente): string {
    $conta = [
        "numeroConta" => uniqid(),
        "cpfCliente" => $cpfCliente,
        "saldo" => 0
    ];
    
    $contas[] = $conta;
    return $conta['numeroConta'];
}

function depositar(&$contas, $numeroConta, $quantia) {
    if ($quantia <= 0) {
        print "Valor inválido para depósito!\n";
        return;
    }
    foreach($contas as &$conta) {
        if($conta['numeroConta'] == $numeroConta) {
            $conta['saldo'] += $quantia;
            print "Depósito de R$ {$quantia} realizado com sucesso na conta {$numeroConta}\n";
            return;
        }
    }
    print "Conta {$numeroConta} não encontrada!\n";
}

function sacar(&$contas, $numeroConta, $quantia) {
    foreach($contas as &$conta) {
        if ($conta['numeroConta'] == $numeroConta) {
            if ($conta['saldo'] >= $quantia) {
                $conta['saldo'] -= $quantia;
                print "Saque de R$ {$quantia} realizado com sucesso na conta {$numeroConta}\n";
            } else {
                print "Saldo insuficiente! \n";
            }
            return;
        }
    }
    print "Conta {$numeroConta} não encontrada!\n";
}

function consultarSaldo(&$contas, $numeroConta) {
    foreach($contas as &$conta) {
        if($conta['numeroConta'] == $numeroConta) {
            print "Saldo da conta {$numeroConta}: R$ {$conta['saldo']}\n";
            return;
        }
    }
    print "Conta {$numeroConta} não encontrada!\n";
}

menu();
