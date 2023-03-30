<?php

namespace App\Domain\Posts;

use App\Domain\Likes\LikeModel;
use App\Domain\Likes\LikeRepository;
use App\Domain\Users\Roles;
use App\Domain\Users\UserModel;
use JsonSerializable;

class PostRoleBasedJSONSerializer implements JsonSerializable
{


    private PostModel $post;
    private ?UserModel $user;

    public function __construct(PostModel $model, ?UserModel $user)
    {
        $this->post = $model;
        $this->user = $user;
    }

    public function jsonSerialize($likes_details = true): array
    {
        $json = $this->post->jsonSerialize();
        if ($this->user === null) {
            return $json;
        }

        if ($this->user->getRole()->isMinimum(Roles::ROLE_PUBLISHER)) {
            $json["attributes"]["likes_count"] = $this->post->getLikesCount();
            $json["attributes"]["dislikes_count"] = $this->post->getDislikesCount();
        }

        if($this->user->getRole()->isMinimum(Roles::ROLE_MODERATOR) && $likes_details){
            $postRepository = new PostRepository();
            $upLikes = $postRepository->fetchLikesUpUsers($this->post->getId());
            $downLikes = $postRepository->fetchLikesDownUsers($this->post->getId());

            $json["attributes"]["up_likes"] = $upLikes;
            $json["attributes"]["down_likes"] = $downLikes;
        }


        return $json;

    }
}