# Future Updates

In the codebase, you will find comments like `#future (Issue #N)`,
where `N` is a 3-digit issue number (e.g., `#future (Issue #001)`).
These comments help track planned features or changes that are
to be implemented in future updates. Below is a list of future
updates and their corresponding issue numbers for easy reference.

## Table of Contents

1. [Issue #001: Load Documents from Previous Application](#001)
2. [Issue #002: Efficient validation for array of ids](#002)
3. [Issue #003: Manage ids of documents that failed or succeed validation](#003)
4. [Issue #004: Not managing correct the data that receive the step 1 of cardApplication create Application](#004)
5. [Issue #005: Using notification to send mail and sms for card updates should work with queueing the notification](#005)

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

### `#003` - **Manage ids of documents that failed or succeed validation** <a id="003"></a>

- **Description**:  
  Manage the documents that failed validation in the request and the documents that no failed.
- **Example**:
  - If a file doesn't belong to the applicant
  - If a file has status that prohibit of editing or deleting
  - if successfully update same documents but some other not.
- **Benefits**:
  - Provides better control of the information available to the end user.
  - Improves error handling and user feedback during operations.
- **Files that will impact?**
  - [DocumentEdit@saveApplication](../resources/js/pages/Card/DocumentEdit.vue)

### `#004` - Not managing correct the data that receive the step 1 of cardApplication create Application

<a id="004"></a>

- **Description**:  
  Somehow is not updated the this.cardApplication on cardApplication.vue and need one more
  fetch to the backend to renew the data.
- **Files that will impact?**
  - [CardApplication@moveStep2](../resources/js/pages/Card/CardApplication.vue)
  - [CardApplicant@create](../resources/js/models/CardApplicant.ts)

//impact #future (Issue #003) -
//see more on /docs/future.md#003

### `#005` - Using notification to send mail and sms for card updates should work with queueing the notification

<a id="005"></a>

- **Description**:  
  Need to find why shouldQueue implementation make tho cardLastUpdate to not be save for later use and fix it or send
  only emails
- **Files that will impact?**
  - [UpdateCardApplicationNotification](../app/Notifications/UpdateCardApplicationNotification.php)
    =
    //impact #future (Issue #003) -
    //see more on /docs/future.md#003
