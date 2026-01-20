<?php
namespace App\Scripts;

class Setup {
    public static function copyAssets() {
        echo "🔄 Configurando assets do Bootstrap e jQuery...\n";
        
        // 1. Copiar Bootstrap
        self::copyBootstrap();
        
        // 2. Copiar jQuery
        self::copyJQuery();
        
        // 3. Remover do vendor
        self::removeFromVendor();
        
        echo "✅ Assets configurados com sucesso!\n";
    }

    private static function copyBootstrap() {
        echo "\n📦 Copiando Bootstrap...\n";
        
        $vendorDir = dirname(__DIR__) . '/vendor/twbs/bootstrap/dist';
        $publicDir = dirname(__DIR__) . '/public/assets/bootstrap';

        if (!is_dir($vendorDir)) {
            echo "❌ ERRO: Bootstrap não encontrado no vendor.\n";
            return;
        }

        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0777, true);
        }

        self::recurseCopy($vendorDir, $publicDir);
        echo "✅ Bootstrap copiado para public/assets/bootstrap\n";
    }

    private static function copyJQuery() {
        echo "\n📦 Copiando jQuery...\n";
        
        $vendorDir = dirname(__DIR__) . '/vendor/components/jquery';
        $publicDir = dirname(__DIR__) . '/public/assets/jquery';

        if (!is_dir($vendorDir)) {
            echo "❌ ERRO: jQuery não encontrado no vendor.\n";
            return;
        }

        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0777, true);
        }

        // Copia apenas os arquivos necessários
        $files = [
            'jquery.min.js',
            'jquery.min.map',
            'jquery.js',
            'jquery.slim.min.js',
            'jquery.slim.min.map'
        ];

        foreach ($files as $file) {
            $src = $vendorDir . '/' . $file;
            $dst = $publicDir . '/' . $file;
            
            if (file_exists($src)) {
                copy($src, $dst);
                echo "  ✓ Copiado: {$file}\n";
            }
        }

        echo "✅ jQuery copiado para public/assets/jquery\n";
    }

    private static function removeFromVendor() {
        echo "\n🗑️  Removendo assets do vendor...\n";

        $packages = [
            dirname(__DIR__) . '/vendor/twbs/bootstrap',
            dirname(__DIR__) . '/vendor/components/jquery'
        ];

        // Remove os pacotes
        foreach ($packages as $dir) {
            self::removeVendorPackage($dir);
        }

        // Remove as pastas pai se estiverem vazias
        $parentDirs = [
            dirname(__DIR__) . '/vendor/twbs',
            dirname(__DIR__) . '/vendor/components'
        ];

        foreach ($parentDirs as $dir) {
            self::removeEmptyDir($dir);
        }

        echo "✅ Vendor limpo\n";
    }

    private static function removeEmptyDir($dir) {
        if (!is_dir($dir)) {
            return;
        }

        // Verifica se o diretório está vazio
        $files = array_diff(scandir($dir), ['.', '..', '.git']);
        
        if (empty($files)) {
            try {
                // Tenta remover com permissão total
                if (@rmdir($dir)) {
                    echo "  ✓ Removido diretório pai: {$dir}\n";
                } else {
                    // Se rmdir falhar, tenta com permissões
                    chmod($dir, 0777);
                    if (rmdir($dir)) {
                        echo "  ✓ Removido diretório pai (com chmod): {$dir}\n";
                    } else {
                        echo "  ⚠️  Falha ao remover: {$dir}\n";
                    }
                }
            } catch (\Exception $e) {
                echo "  ⚠️  Exceção ao remover {$dir}: {$e->getMessage()}\n";
            }
        } else {
            echo "  ℹ️  Diretório não vazio: {$dir} (conteúdo: " . implode(', ', $files) . ")\n";
        }
    }

    private static function removeVendorPackage($dir) {
        if (!is_dir($dir)) {
            return;
        }

        // Remove tudo, inclusive .git
        self::recursiveRemove($dir);

        // Tenta remover o diretório pai
        try {
            if (is_dir($dir)) {
                @rmdir($dir);
                echo "  ✓ Removido: {$dir}\n";
            }
        } catch (\Exception $e) {
            echo "  ⚠️  Não foi possível remover: {$dir}\n";
        }
    }

    private static function recurseCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private static function recursiveRemove($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $fullPath = $dir . DIRECTORY_SEPARATOR . $object;
                    
                    if (is_dir($fullPath) && !is_link($fullPath)) {
                        self::recursiveRemove($fullPath);
                    } else {
                        try {
                            if (file_exists($fullPath)) {
                                // Remove permissões de apenas-leitura (Windows)
                                @chmod($fullPath, 0777);
                                @unlink($fullPath);
                            }
                        } catch (\Exception $e) {
                            // Continua mesmo se houver erro
                        }
                    }
                }
            }
            
            // Tenta remover o diretório
            try {
                @chmod($dir, 0777);
                @rmdir($dir);
            } catch (\Exception $e) {
                // Ignorar erros
            }
        }
    }
}
