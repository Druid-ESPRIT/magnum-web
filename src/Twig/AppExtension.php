<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
	public function getTests()
	{
		return [
			new TwigTest('instanceof', [$this, 'isInstanceof'])
		];
	}

    public function getFunctions()
    {
        return [
            new TwigFunction('get_class', 'get_class'),
        ];
    }

	/**
	 * @param $var
	 * @param $instance
	 * @return bool
	 */
	public function isInstanceof($var, $instance) {
		return $var instanceof $instance;
	}

    public function getName()
    {
        return 'class_twig_extension';
    }
}
