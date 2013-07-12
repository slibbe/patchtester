<?php
/**
 * User: elkuku
 * Date: 08.09.12
 * Time: 19:08
 *
 * @property-read  PTGithubRepos  $repos  GitHub API object for repos.
 */
class PTGithub extends JGithub
{
    /**
     * @var    PTGithubRepos
     */
    protected $repos;

    public static function getInstance(JRegistry $options = null, JGithubHttp $client = null)
    {
        return new PTGithub($options, $client);
    }

    public function __get($name)
    {
        if ($name == 'repos')
        {
            if ($this->repos == null)
            {
                $this->repos = new PTGithubRepos($this->options, $this->client);
            }

            return $this->repos;
        }

        return parent::__get($name);
    }
}
