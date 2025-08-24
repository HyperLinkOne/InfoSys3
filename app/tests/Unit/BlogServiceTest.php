<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Service\BlogService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BlogServiceTest extends TestCase
{
    private BlogService $service;
    private MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BlogPostRepository::class);
        $this->service = new BlogService($this->repository);
    }

    public function testGetAllPosts(): void
    {
        $expectedPosts = [
            new BlogPost(1, 'Test Title', 'Test Content', new \DateTimeImmutable(), null)
        ];

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedPosts);

        $result = $this->service->getAllPosts();
        $this->assertEquals($expectedPosts, $result);
    }

    public function testGetPost(): void
    {
        $expectedPost = new BlogPost(1, 'Test Title', 'Test Content', new \DateTimeImmutable(), null);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($expectedPost);

        $result = $this->service->getPost(1);
        $this->assertEquals($expectedPost, $result);
    }

    public function testCreatePostWithValidData(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('insert')
            ->with('Test Title', 'Test Content')
            ->willReturn(1);

        $result = $this->service->createPost('Test Title', 'Test Content');
        $this->assertEquals(1, $result);
    }

    public function testCreatePostWithEmptyTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title cannot be empty');

        $this->service->createPost('', 'Test Content');
    }

    public function testCreatePostWithEmptyContent(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content cannot be empty');

        $this->service->createPost('Test Title', '');
    }

    public function testUpdatePostWithValidData(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, 'Updated Title', 'Updated Content')
            ->willReturn(true);

        $result = $this->service->updatePost(1, 'Updated Title', 'Updated Content');
        $this->assertTrue($result);
    }

    public function testDeletePost(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('softDelete')
            ->with(1)
            ->willReturn(true);

        $result = $this->service->deletePost(1);
        $this->assertTrue($result);
    }
}
