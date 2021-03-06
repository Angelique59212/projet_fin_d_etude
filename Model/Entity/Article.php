<?php

namespace App\Model\Entity;

use AbstractEntity;


class Article extends AbstractEntity
{
    private string $title;
    private string $content;
    private string $summary;
    private string $image;
    private User $author;


    /**
     * @return string
     */
    public function getSummary(): string
    {
        return html_entity_decode($this->summary);
    }

    /**
     * @param string $summary
     * @return Article
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return html_entity_decode($this->content);
    }

    /**
     * @param string $content
     * @return Article
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Article
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Article
     */
    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }
}