<?php

namespace JMS\JobQueueBundle\Twig;

class JobQueueExtension extends \Twig_Extension
{
    private $linkGenerators = array();

    public function __construct(array $generators = array())
    {
        $this->linkGenerators = $generators;
    }

    public function getTests(): array
    {
        return array(
            new \Twig_SimpleTest('jms_job_queue_linkable', array($this, 'isLinkable'))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new \Twig_SimpleFunction('jms_job_queue_path', array($this, 'generatePath'), array('is_safe' => array('html' => true)))
        );
    }

    public function getFilters(): array
    {
        return array(
            new \Twig_SimpleFilter('jms_job_queue_linkname', array($this, 'getLinkname')),
            new \Twig_SimpleFilter('jms_job_queue_args', array($this, 'formatArgs'))
        );
    }

    public function formatArgs(array $args, $maxLength = 60): string
    {
        $str = '';
        $first = true;
        foreach ($args as $arg) {
            $argLength = strlen($arg);

            if ( ! $first) {
                $str .= ' ';
            }
            $first = false;

            if (strlen($str) + $argLength > $maxLength) {
                $str .= substr($arg, 0, $maxLength - strlen($str) - 4).'...';
                break;
            }

            $str .= escapeshellarg($arg);
        }

        return $str;
    }

    public function isLinkable($entity): bool
    {
        foreach ($this->linkGenerators as $generator) {
            if ($generator->supports($entity)) {
                return true;
            }
        }

        return false;
    }

    public function generatePath($entity)
    {
        foreach ($this->linkGenerators as $generator) {
            if ($generator->supports($entity)) {
                return $generator->generate($entity);
            }
        }

        throw new \RuntimeException(sprintf('The entity "%s" has no link generator.', get_class($entity)));
    }

    public function getLinkname($entity)
    {
        foreach ($this->linkGenerators as $generator) {
            if ($generator->supports($entity)) {
                return $generator->getLinkname($entity);
            }
        }

        throw new \RuntimeException(sprintf('The entity "%s" has no link generator.', get_class($entity)));
    }

    public function getName(): string
    {
        return 'jms_job_queue';
    }
}