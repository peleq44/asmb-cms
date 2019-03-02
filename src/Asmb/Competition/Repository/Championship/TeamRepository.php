<?php

namespace Bundle\Asmb\Competition\Repository\Championship;

use Bolt\Storage\Repository;
use Bolt\Translation\Translator as Trans;
use Bundle\Asmb\Competition\Entity\Championship\Pool;
use Bundle\Asmb\Competition\Entity\Championship\Team;

/**
 * Repository for champioship teams.
 *
 * @author    Perrine Léquipé <perrine.lequipe@gmail.com>
 * @copyright 2019
 */
class TeamRepository extends Repository
{
    /**
     * Return all teams group by category name.
     *
     * @return bool|mixed|object[]
     * @throws \Bolt\Exception\InvalidRepositoryException
     */
    public function findAllGroupByCategoryName()
    {
        $teamsGroupedByCategoryName = [];

        $categories = $this->getEntityManager()->getRepository('championship_category')
            ->findBy([], ['position', 'ASC']);

        // Init $teamsGroupedByCategoryName with category name SORTED BY POSITION
        /** @var \Bundle\Asmb\Competition\Entity\Championship\Category $category */
        foreach ($categories as $category) {
            $teamsGroupedByCategoryName[$category->getName()] = [];
        }

        $teams =  $this->findBy([], ['name', 'ASC']);
        /** @var \Bundle\Asmb\Competition\Entity\Championship\Team $team */
        foreach ($teams as $team) {
            $teamsGroupedByCategoryName[$team->getCategoryName()][] = $team;
        }

        return $teamsGroupedByCategoryName;
    }

    /**
     * @param string $categoryName
     *
     * @return array
     */
    public function findByCategoryNameAsChoices($categoryName)
    {
        $asChoices = [];
        $teams = $this->findBy(['category_name' => $categoryName], ['short_name', 'ASC']);

        /** @var \Bundle\Asmb\Competition\Entity\Championship\Team $team */
        foreach ($teams as $team) {
            $asChoices[$team->getId()] = $team->getShortName();
        }

        return $asChoices;
    }

    /**
     * @param \Bundle\Asmb\Competition\Entity\Championship\Pool $pool
     *
     * @return array
     */
    public function findByPoolIdAsChoices(Pool $pool)
    {
        $asChoices = [];
        $teams = $this->findByPool($pool);

        /** @var \Bundle\Asmb\Competition\Entity\Championship\Team $team */
        foreach ($teams as $team) {
            $asChoices[$team->getId()] = $team->getShortName();
        }

        return $asChoices;
    }

    /**
     * Find teams for given Pool.
     *
     * @param \Bundle\Asmb\Competition\Entity\Championship\Pool $pool
     *
     * @return bool|mixed|Team[]
     */
    public function findByPool(Pool $pool)
    {
        $teamIds = array_keys($pool->getTeams());
        $teams = $this->findBy(['id' => $teamIds], ['short_name', 'ASC']);
        $teams = (false !== $teams) ? $teams : [];

        return $teams;
    }
}
