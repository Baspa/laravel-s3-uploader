# Contributing

Contributions are welcome and will be fully credited.

## Pull Requests

- **Add tests** — your patch won't be accepted if it doesn't have tests.
- **Document any change in behaviour** — make sure the `README.md` and any other relevant documentation are kept up-to-date.
- **Keep the code style consistent** — run `composer format` (Pint) before committing.
- **Make sure the test suite and static analysis pass** — run `composer test` and `composer analyse`.
- **One pull request per feature** — if you want to do more than one thing, send multiple pull requests.

## Running the checks locally

```bash
composer test      # run the test suite
composer analyse   # run PHPStan (level 8)
composer format    # apply the code style fixes
```

**Happy coding!**
