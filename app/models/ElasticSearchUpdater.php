<?php

namespace Fitak;

use ElasticSearch;
use Kdyby;
use Nette;
use Nextras\Orm;


class ElasticSearchUpdater extends Nette\Object implements Kdyby\Events\Subscriber
{

	/** @var ElasticSearch */
	private $elastic;

	public function __construct(ElasticSearch $elastic)
	{
		$this->elastic = $elastic;
	}

	/**
	 * @inheritdoc
	 */
	public function getSubscribedEvents()
	{
		return [
			'Nextras\Orm\Repository\Repository::onAfterInsert' => 'onAfterInsert',
			'Nextras\Orm\Repository\Repository::onAfterUpdate' => 'onAfterUpdate',
		];
	}

	private function getDefaultData(Post $post)
	{
		$message = trim(implode(', ', [
			$post->getMessageWithoutTags(),
			$post->description,
			$post->caption,
		]));
		return [
			'tags' => $post->getParsedTags()[0],
			'message' => $message,
			'message_raw' => $post->message,
			'likes' => $post->likesCount,
		];
	}

	public function onAfterInsert(Orm\Entity\IEntity $post)
	{
		if ($post instanceof Post)
		{
			$this->elastic->addToIndex(ElasticSearch::TYPE_CONTENT, $post->id, [
				'author' => $post->fromName,
				'is_topic' => ($post->parent === NULL),
				'created_time' => $post->createdTime->getTimestamp(),
				'group' => $post->group->id,
			] + $this->getDefaultData($post));
		}
	}

	public function onAfterUpdate(Orm\Entity\IEntity $post)
	{
		if ($post instanceof Post)
		{
			$this->elastic->addToIndex(ElasticSearch::TYPE_CONTENT, $post->id, $this->getDefaultData($post));
		}
	}

}
