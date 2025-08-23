# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Testing
- `make test` or `./vendor/bin/simple-phpunit` - Run PHPUnit tests
- Tests are configured in `phpunit.xml.dist` with test environment setup

### Code Quality
- `make lint` or `composer run lint` - Run both PHPStan and PHP_CodeSniffer
- `make phpstan` or `composer run lint:phpstan -- --memory-limit=1G` - Run static analysis with PHPStan
- `make phpcs` or `composer run lint:phpcs` - Check code style with PHP_CodeSniffer
- `make phpcs-fix` or `composer run lint:phpcs:fix` - Fix code style issues automatically
- Code follows PSR12 standards (defined in `phpcs.xml`)
- PHPStan configured at level 5 with specific ignores for Doctrine/Sonata patterns

### Dependencies
- `make install` or `composer install` - Install dependencies
- `make update` or `composer update` - Update dependencies

## Architecture Overview

This is a Symfony bundle for managing video game records and rankings. Key architectural components:

### Core Domain Entities
- **Player**: Core user entity with ranking data, badges, and statistics
- **Game/Group/Chart**: Hierarchical game structure (Games contain Groups, Groups contain Charts)
- **PlayerChart**: Player scores/records on specific charts with proof validation
- **Team**: Team-based competitions and rankings
- **Badge/Proof**: Achievement and verification system
- **Video**: Video proof integration with YouTube API

### Data Flow & Ranking System
- **Message/MessageHandler**: Asynchronous ranking updates using Symfony Messenger
- **Event/EventSubscriber**: Domain event handling for rank recalculations
- **Repository**: Custom queries for complex ranking and statistical data
- **DataProvider**: API Platform data providers for rankings and statistics

### API Architecture
- Built on API Platform with extensive filtering and custom endpoints
- RESTful endpoints in `src/Controller/` following resource-based structure
- Custom controllers for complex operations (rankings, statistics, autocomplete)

### Admin Interface
- Sonata Admin bundle integration for backend management
- Custom admin controllers and views in `src/Admin/` and `Resources/views/Admin/`

### Key Features
- Multi-level ranking system (players, teams, games, platforms, countries)
- Proof validation workflow with video/image upload
- Badge/achievement system with automated awarding
- YouTube integration for video proofs
- Translation support with Gedmo translatable entities
- Message queue processing for performance-critical ranking updates

### File Structure Patterns
- Entities use extensive traits for shared ranking/statistical properties
- Controllers organized by resource type with nested sub-resources
- Separate message handlers for each ranking update type
- Repository pattern with custom query methods for complex data retrieval

### Testing
- Functional tests in `tests/Functional/Api/` for API endpoints
- Test kernel setup with separate test database configuration
- Uses Symfony's testing framework with PHPUnit bridge