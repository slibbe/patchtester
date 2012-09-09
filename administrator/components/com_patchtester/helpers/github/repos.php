<?php
/**
 * User: elkuku
 * Date: 08.09.12
 * Time: 18:54
 */

/**
 * A.
 */
class PtGithubRepos extends JGithubObject
{
    public function get($user, $repo)
    {
        $path = '/repos/'.$user.'/'.$repo;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // Validate the response code.
        if($response->code != 200)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);

            throw new DomainException($error->message, $response->code);
        }

        return json_decode($response->body);
    }

    /**
     * @param string $user
     * @param string $type       all, owner, public, private, member. Default: all.
     * @param string $sort       created, updated, pushed, full_name, default: full_name.
     * @param string $direction  asc or desc, default: when using full_name: asc, otherwise desc.
     *
     * @return mixed
     * @throws DomainException
     */
    public function getPublicRepos($user, $type = 'all', $sort = 'full_name', $direction = 'desc')
    {
        $path = '/users/'.$user.'/repos';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // Validate the response code.
        if($response->code != 200)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);
            throw new DomainException($error->message, $response->code);
        }

        return json_decode($response->body);
    }
}
