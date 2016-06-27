# NetPivot
The 3 main branches are:

* `develop`: This branch is used for promoting unreviwed/untested changes from
   developer's local working copies. This branch is **highly** active. Tagging
   releases **must** be avoided.
* `staging`: This branch is used for testing validated and finished code. This
   branch receives only *pull requests* coming from `staging` and it should be
   updated daily. Tagging releases is optional.
* `master`: This branch is used to *freeze* tested, validated, reviewed and
   stable code. This branch receives only *pull requests* coming from `develop`
   and it should be updated weekly. Tagging releases is encouraged.

For more details on handling branches, please check the `docs/branches.md`
document.

