<?php



class ClassLoaderTest extends \PHPUnit\Framework\TestCase {

    public function testFile(){
        $f = "src/mf/utils/ClassLoader.php";
        $this->assertFileExists($f,
          'FEEDBACK : La classe CalssLoader doit Ãªtre dans le fichier '.$f.'.');
    }

    public function testNamespace(){
        $f = "src/mf/utils/ClassLoader.php";
        $n = "\mf\utils\ClassLoader";
        $af = "src/mf/utils/AbstractClassLoader.php";
        $an = "\mf\utils\AbstractClassLoader";

        require_once ($af);
        require_once ($f);
        $this->assertTrue(class_exists($n),
          'FEEDBACK La classe ClassLoader doit appartenir Ã  l\'espace de noms '.$n.'.');
    }

	/**
 	 * getPrivateMethod
 	 *
 	 * @author	Joe Sexton <joe@webtipblog.com>
 	 * @param 	string $className
 	 * @param 	string $methodName
 	 * @return	ReflectionMethod
 	 */
	public function getPrivateMethod( $className, $methodName ) {
		$reflector = new ReflectionClass( $className );
		$method = $reflector->getMethod( $methodName );
		$method->setAccessible( true );
		return $method;
	}


    public function testMakePath(){
        $prefix = "src";
        $filename = "un/chemain/vers/le/fichier.php";

		$object = new  \mf\utils\ClassLoader($prefix);

		$method = $this->getPrivateMethod('\mf\utils\ClassLoader',
                                          'makePath' );

		$result = $method->invokeArgs( $object, array( $filename ) );

		$this->assertEquals( $prefix.DIRECTORY_SEPARATOR.$filename, $result,
            "FEEDBACK : la mÃ©thode makePath doit ajouter le prefixe au dÃ©but du chemin vers le fichier et les sÃ©parer par le DIRECTORY_SEPARATOR.");
	}


    public function testGetFilename(){

        $prefix = "src";
        $classname = "un\\nom\\complet\\de\\classe";

		$object = new  \mf\utils\ClassLoader($prefix);

		$method = $this->getPrivateMethod( '\mf\utils\ClassLoader',
                                           'getFilename' );

		$result = $method->invokeArgs( $object, array( $classname ) );

        $back_slashes = strpbrk($result, "\\");

        $this->assertFalse($back_slashes, "FEEDBACK : la mÃ©thode makePath doit remplacer tout les '\\' par des DIRECTORY_SEPARATOR.");

        $slash = substr_compare($result, '/', 0, 1);

        $this->assertFalse($slash==1, "FEEDBACK : la mÃ©thode makePath doit enlever le premier /.");

        $ext = substr_compare($result, '.php', -4, 4);

        $this->assertFalse($ext==1, "FEEDBACK : la mÃ©thode makePath doit ajouter l'extention '.php' Ã  la fin de la chaine");

        $this->assertEquals(  "un/nom/complet/de/classe.php", $result,
            "FEEDBACK : la mÃ©thode makePath doit  effacer le premier caractÃ¨re '\', remplacer toute les autres occurrences du caractÃ¨re '\\' par la constante DIRECTORY_SEPARATOR et ajouter '.php' a la fin de la chaine ");

    }


    function testNotFound() {
        $prefix = "src";
        $classname = "\une\clsse\inexistante";
        $cl = new \mf\utils\ClassLoader($prefix);

        set_error_handler(function() {
             echo "FEEDBACK : La mÃ©thodes loadClass ne dois pas gÃ©nÃ©rer d'erreur si le fichier n'existe pas\n";
        });

        $cl->loadClass($classname);
        restore_error_handler();
        $this->assertTrue(TRUE);

    }

    function testLoadClass() {
        require_once('src/mf/utils/AbstractClassLoader.php');
        require_once('src/mf/utils/ClassLoader.php');
        $cl = new \mf\utils\ClassLoader('tests');
        $cl -> register();
        spl_autoload_call('\dummy\classname\Name');
        $this->assertTrue(class_exists('\dummy\classname\Name'), "FEEDBACK : l'auto chargement ne se rÃ©alise pas correctement.");
        spl_autoload_unregister([$cl, "loadClass"]);
    }


}
