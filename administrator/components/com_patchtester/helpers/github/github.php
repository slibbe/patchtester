<?php
/**
 * User: elkuku
 * Date: 08.09.12
 * Time: 19:08
 *
 * @property-read  PtGithubRepos  $repos  GitHub API object for repos.
 */
class PtGithub extends JGithub
{
    /**
     * @var    PtGithubRepos
     */
    protected $repos;

    public static function getInstance(JRegistry $options = null, JGithubHttp $client = null)
    {
        return new PtGithub($options, $client);
    }

    public function __get($name)
    {
        if ($name == 'repos')
        {
            if ($this->repos == null)
            {
                $this->repos = new PtGithubRepos($this->options, $this->client);
            }

            return $this->repos;
        }

        return parent::__get($name);
    }
}
