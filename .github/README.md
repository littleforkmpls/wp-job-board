# WP Job Board

### Releases

Create a release

1. Find all references to the version in the codebase and update to the new version number.
1. Run `./bin/build-zip.sh` from the root of the project. Enter the version number when prompted
1. Create a new release on GitHub.
1. Attach the generated zip file to the release - `dist/wp-job-board.{VER}.zip`
1. Update `https://plugins.little-fork.com/wp-job-board/plugin-wp-job-board.json` with new version number and download link.
