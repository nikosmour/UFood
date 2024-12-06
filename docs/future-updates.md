# Future Updates

In the codebase, you will find comments like `#future (Issue #N)`,
where `N` is a 3-digit issue number (e.g., `#future (Issue #001)`).
These comments help track planned features or changes that are
to be implemented in future updates. Below is a list of future
updates and their corresponding issue numbers for easy reference.

## Table of Contents

1. [Issue #001: Load Documents from Previous Application](#001)
2. [Issue #002: Efficient validation for array of ids](#002)

## Future Updates List

### `#001` - **Load Documents from Previous Application** <a id="001"></a>

- **Description**:  
  We need to transfer the documents from the previous application
  that stay the same across the years.
- **Example**:  
  Files such as the academic card or government ID.
- **Benefits**:
    - **Applicant**: When a user create a new application could already see in the upload files to be inported
      files that other ways he will have to spend time to upload them again.
    - **Application Staff**: The user will see already approved files and depending on the policy of the company
      possibly will not have to re-check them
- **Files that will impact?**
    - [CardApplicationController@store](../app/Http/Controllers/CardApplicationController.php)
  - [CardApplicationCreateForm@createApplication](../resources/js/pages/Card/CardApplicationCreateForm.vue)

### `#002` - **Efficient validation for array of ids** <a id="002"></a>

- **Description**:  
  Optimize validation for an array of IDs in the request, especially when checking if the IDs exist in the database.
- **Example**:  
  Instead of using `'documents.delete.*' => 'integer|exists:card_application_documents,id'`, use a custom rule like:
  `'documents.delete' => 'integer|custom:card_application_documents,id'`
- **Benefits**:
    - Improves readability and efficiency by reducing the complexity of validating arrays.
    - Provides better control over the validation logic for related data.
    - Reduces database queries, making the process more efficient.
- **Files that will impact?**
    - [UpdateCardApplicationRequest](../app/Http/Requests/UpdateCardApplicationRequest.php)
