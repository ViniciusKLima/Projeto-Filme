<?php
/**
 * Script de Seed para importar filmes em lote
 * 
 * Uso: php bin/seed-filmes.php "path/to/filmes.json"
 * 
 * Formato JSON esperado:
 * [
 *   {
 *     "nome": "Nome do Filme",
 *     "sinopse": "Descrição...",
 *     "capaPrincipal": "https://...",
 *     "capaFundo": "https://...",
 *     "anoLancamento": 2024,
 *     "diretor": "Nome do Diretor",
 *     "elenco": "Ator 1, Ator 2",
 *     "genero": "Ação, Drama",
 *     "nota": 8.5,
 *     "trailer": "https://youtube.com/...",
 *     "streaming": "Netflix, Prime Video"
 *   },
 *   ...
 * ]
 */

// Caminho para o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Importar classes necessárias
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Application\Entity\Filme;

// Verificar se arquivo foi passado
if ($argc < 2) {
    echo "Uso: php bin/seed-filmes.php <caminho-para-arquivo.json>\n";
    echo "Exemplo: php bin/seed-filmes.php filmes.json\n";
    exit(1);
}

$arquivoJson = $argv[1];

// Verificar se arquivo existe
if (!file_exists($arquivoJson)) {
    echo "❌ Erro: Arquivo '$arquivoJson' não encontrado.\n";
    exit(1);
}

// Ler JSON
$json = file_get_contents($arquivoJson);
$filmes = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ Erro ao decodificar JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

if (!is_array($filmes) || empty($filmes)) {
    echo "❌ Erro: JSON deve ser um array não-vazio de filmes.\n";
    exit(1);
}

echo "📽️  Preparando seed de " . count($filmes) . " filme(s)...\n\n";

// ========== Configurar Doctrine ==========
try {
    $paths = [__DIR__ . '/../module/Application/src/Entity'];
    $isDevMode = true;

    // Usar configuração por attributes (Doctrine ORM 3)
    if (class_exists(ORMSetup::class) && method_exists(ORMSetup::class, 'createAttributeMetadataConfiguration')) {
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
    } else {
        throw new RuntimeException('Nenhuma API do Doctrine disponível para configuração de metadata.');
    }

    // Carregar configuração do banco
    $configPath = __DIR__ . '/../config/autoload/doctrine.local.php';
    if (file_exists($configPath)) {
        $dbConfig = include $configPath;
        $connectionParams = $dbConfig['doctrine']['connection']['params'] ?? [];
    } else {
        // Fallback para valores padrão
        $connectionParams = [
            'driver'   => 'pdo_mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'user'     => 'root',
            'password' => '',
            'dbname'   => 'projeto_filmes',
            'charset'  => 'utf8mb4',
        ];
    }

    // Criar EntityManager
    $connection = DriverManager::getConnection($connectionParams);
    $em = new EntityManager($connection, $config);

    echo "✅ Conectado ao banco de dados: " . $connectionParams['dbname'] . "\n";
    echo "   Host: " . $connectionParams['host'] . "\n\n";

} catch (Exception $e) {
    echo "❌ Erro ao conectar ao banco: " . $e->getMessage() . "\n";
    exit(1);
}

// ========== Inserir filmes ==========
$sucesso = 0;
$erro = 0;
$duplicado = 0;

foreach ($filmes as $index => $dados) {
    try {
        // Validar campos obrigatórios
        if (empty($dados['nome'])) {
            echo "⚠️  Filme #" . ($index + 1) . ": Campo 'nome' vazio, pulando...\n";
            $erro++;
            continue;
        }

        // Verificar se filme já existe pelo nome
        $repo = $em->getRepository(Filme::class);
        $existe = $repo->findOneBy(['nome' => trim($dados['nome'])]);
        
        if ($existe) {
            echo "⏭️  Filme #" . ($index + 1) . ": \"" . $dados['nome'] . "\" já existe no banco, pulando...\n";
            $duplicado++;
            continue;
        }

        // Criar entidade Filme
        $filme = new Filme();
        $filme->setNome($dados['nome'] ?? '');
        $filme->setSinopse($dados['sinopse'] ?? '');
        $filme->setCapaPrincipal($dados['capaPrincipal'] ?? '');
        $filme->setCapaFundo($dados['capaFundo'] ?? '');
        $filme->setAnoLancamento($dados['anoLancamento'] ?? null);
        $filme->setDiretor($dados['diretor'] ?? '');
        $filme->setElenco($dados['elenco'] ?? '');
        $filme->setGenero($dados['genero'] ?? '');
        $filme->setNota($dados['nota'] ?? null);
        $filme->setTrailer($dados['trailer'] ?? '');
        $filme->setStreaming($dados['streaming'] ?? '');

        // Persistir
        $em->persist($filme);
        echo "✓ Filme #" . ($index + 1) . ": " . $filme->getNome() . "\n";
        $sucesso++;

    } catch (Exception $e) {
        echo "❌ Filme #" . ($index + 1) . ": Erro - " . $e->getMessage() . "\n";
        $erro++;
    }
}

// ========== Flush (salvar todos) ==========
try {
    $em->flush();
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ Sucesso! $sucesso filme(s) inserido(s) no banco de dados.\n";
    if ($duplicado > 0) {
        echo "⏭️  $duplicado filme(s) duplicado(s) (já existia no banco).\n";
    }
    if ($erro > 0) {
        echo "⚠️  $erro filme(s) falharam.\n";
    }
    echo str_repeat("=", 60) . "\n";
} catch (Exception $e) {
    echo "\n❌ Erro ao salvar filmes: " . $e->getMessage() . "\n";
    exit(1);
}
