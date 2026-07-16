<?php
/**
 * setup_db.php - Importador Automático e Seguro do Banco de Dados
 * Pode ser acessado via navegador ou linha de comando para recriar as tabelas.
 */
include("_config.inc.php");

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9;'>";
echo "<h2 style='color: #0056b3;'>🚀 Setup do Banco de Dados - Grupo Fassil</h2>";

// Conectar ao MySQL
$mysqli = @new mysqli(HOST, USER, PASS, DBSA);

if ($mysqli->connect_error) {
    die("<p style='color: red;'><b>Erro ao conectar ao MySQL:</b> (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "</p></div>");
}

echo "<p>✅ Conectado ao servidor MySQL com sucesso no host <b>" . HOST . "</b> (banco: <b>" . DBSA . "</b>).</p>";

// Definir charset e timezone
$mysqli->set_charset("utf8");
$mysqli->query("SET TIME_ZONE = '-04:00'");

// Verifica se a tabela conteudo já existe e tem registros
$check = $mysqli->query("SHOW TABLES LIKE 'conteudo'");
if ($check && $check->num_rows > 0) {
    $rowCheck = $mysqli->query("SELECT count(*) as total FROM conteudo");
    $total = $rowCheck ? $rowCheck->fetch_assoc()['total'] : 0;
    if ($total > 0 && !isset($_GET['force'])) {
        echo "<p style='color: green; font-size: 16px;'><b>O banco de dados já está importado e funcional!</b> (Tabela <i>conteudo</i> encontrada com $total registros).</p>";
        echo "<p><a href='index.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Ir para o Site Principal</a> ";
        echo "<a href='gerenciar/' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;'>Ir para o Painel Administrativo</a></p>";
        echo "<p><small>Se desejar forçar a re-importação do dump SQL do zero, acesse <a href='setup_db.php?force=1'>setup_db.php?force=1</a>.</small></p>";
        echo "</div>";
        exit;
    }
}

echo "<p>⏳ Iniciando o download e importação do arquivo SQL oficial do repositório GitHub...</p>";

// Caminho local (se copiado no Docker) ou URL do GitHub
$localFile = "/var/www/html/init-db/01-grupofas_sistema.sql";
$gitUrl = "https://raw.githubusercontent.com/cristhian-sancore/sistema-fassil1/main/init-db/01-grupofas_sistema.sql";

$sqlContent = "";
if (file_exists($localFile)) {
    echo "<p>📁 Lendo arquivo SQL local do container (<code>$localFile</code>)...</p>";
    $sqlContent = file_get_contents($localFile);
} else {
    echo "<p>🌐 Baixando arquivo SQL oficial diretamente do GitHub (<code>main</code> branch)...</p>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gitUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $sqlContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || empty($sqlContent)) {
        die("<p style='color: red;'><b>Erro ao baixar o arquivo SQL do GitHub (HTTP $httpCode).</b> Verifique sua conexão à internet no container.</p></div>");
    }
}

if (empty(trim($sqlContent))) {
    die("<p style='color: red;'><b>Erro:</b> O conteúdo do arquivo SQL está vazio.</p></div>");
}

echo "<p>🔄 Executando comandos SQL (importando 37 tabelas e dados iniciais)...</p>";
flush();

// Executar multi_query para importar todas as tabelas
if ($mysqli->multi_query($sqlContent)) {
    $tabelasCriadas = 0;
    do {
        // Consumir resultados das queries
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
        $tabelasCriadas++;
    } while ($mysqli->more_results() && $mysqli->next_result());

    if ($mysqli->error) {
        echo "<p style='color: darkred;'>⚠️ Aviso durante a importação: (" . $mysqli->errno . ") " . $mysqli->error . "</p>";
    } else {
        echo "<p style='color: green; font-size: 18px;'><b>🎉 Banco de Dados 'grupofas_sistema' importado com sucesso!</b></p>";
        echo "<p>Mais de $tabelasCriadas comandos SQL executados sem nenhum erro.</p>";
    }
} else {
    echo "<p style='color: red;'><b>Erro ao executar SQL:</b> (" . $mysqli->errno . ") " . $mysqli->error . "</p>";
}

echo "<hr style='margin: 20px 0;'>";
echo "<p><a href='index.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; font-weight: bold; border-radius: 4px;'>Acessar o Site Principal</a> ";
echo "<a href='gerenciar/' style='display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; font-weight: bold; border-radius: 4px; margin-left: 10px;'>Acessar o Painel Administrativo</a></p>";
echo "</div>";
