# Future Updates

In the codebase, you will find comments like `#future (Issue #N)`,
where `N` is a 3-digit issue number (e.g., `#future (Issue #001)`).
These comments help track planned features or changes that are
to be implemented in future updates. Below is a list of future
updates and their corresponding issue numbers for easy reference.

## Table of Contents

1. [Issue #001: Load Documents from Previous Application](#001)

## Future Updates List

### `#001` - **Load Documents from Previous Application** <a id="001"></a>

- **Description**: We need to transfer the documents from the previous application
  that stay the same across the years.
- **Example**: Files such as the academic card or government ID.
- **Benefits**:
    - **Applicant**: When a user create a new application could already see in the upload files to be inported
      files that other ways he will have to spend time to upload them again.
    - **Application Staff**: The user will see already approved files and depending on the policy of the company
      possibly will not have to re-check them
- **Files that will impact?**
    - [CardApplicationController@store](../app/Http/Controllers/CardApplicationController.php)
  - [CardApplicationCreateForm@createApplication](../resources/js/pages/Card/CardApplicationCreateForm.vue)