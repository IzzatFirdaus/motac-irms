/**
 * MOTAC IRMS - User Management JavaScript Module
 * Optimized for clarity, maintainability, and comprehensive documentation.
 * Handles user DataTable, AJAX CRUD operations, form validation, and UI interactivity.
 *
 * Features:
 * - Server-side DataTables with responsive design
 * - AJAX-powered CRUD operations (Create, Read, Update, Delete)
 * - Form validation using FormValidation plugin
 * - SweetAlert2 integration for user confirmations
 * - Input masking for phone numbers
 * - Export functionality (Print, CSV, Excel, PDF, Copy)
 * - Bootstrap 5 integration with offcanvas forms
 *
 * Dependencies:
 * - jQuery 3.x
 * - DataTables with responsive extension
 * - FormValidation plugin
 * - SweetAlert2
 * - Select2
 * - Cleave.js (for input masking)
 * - Bootstrap 5
 *
 * Last Updated: 2025-08-07 14:36:53 UTC
 * Updated By: IzzatFirdaus
 *
 * @author MOTAC Development Team
 * @version 2.1.0
 */

'use strict';

$(function () {
  // =============================================
  // VARIABLE DECLARATIONS AND CONFIGURATION
  // =============================================

  /**
   * Core DOM element references and configuration variables
   * These variables store references to key DOM elements and configuration values
   * used throughout the user management functionality
   */

  // DataTable element - main users table container
  var dt_user_table = $('.datatables-users'),

      // Select2 dropdown elements for enhanced select boxes
      select2 = $('.select2'),

      // Base URL for user view pages - dynamically constructed
      userView = baseUrl + 'app/user/view/account',

      // Offcanvas form element for add/edit user operations
      offCanvasForm = $('#offcanvasAddUser'),

      // Form element reference for validation
      addNewUserForm = document.getElementById('addNewUserForm'),

      // DataTable instance variable (will be populated after initialization)
      dt_user;

  // =============================================
  // SELECT2 INITIALIZATION
  // =============================================

  /**
   * Initialize Select2 enhanced dropdowns
   * Select2 provides searchable dropdowns with better UX
   * We wrap each select2 element to ensure proper dropdown positioning
   */
  if (select2.length) {
    select2.each(function() {
      var $this = $(this);

      // Wrap in position-relative container for proper dropdown positioning
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: $this.data('placeholder') || 'Select an option',
        allowClear: true,
        width: '100%',
        dropdownParent: $this.parent() // Ensures dropdown stays within parent container
      });
    });
  }

  // =============================================
  // AJAX CONFIGURATION
  // =============================================

  /**
   * Global AJAX setup for Laravel CSRF protection
   * This ensures all AJAX requests include the CSRF token required by Laravel
   * The token is retrieved from the meta tag in the HTML head
   */
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      'X-Requested-With': 'XMLHttpRequest' // Identifies requests as AJAX
    },
    // Global error handler for AJAX requests
    error: function(xhr, status, error) {
      console.error('AJAX Error:', {
        status: xhr.status,
        statusText: xhr.statusText,
        responseText: xhr.responseText,
        error: error
      });

      // Show user-friendly error message
      if (window.Swal) {
        Swal.fire({
          icon: 'error',
          title: 'Connection Error',
          text: 'An error occurred while communicating with the server. Please try again.',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }
    }
  });

  // =============================================
  // DATATABLES INITIALIZATION
  // =============================================

  /**
   * Initialize DataTables for the users table
   * This creates a server-side processed table with advanced features:
   * - Server-side processing for large datasets
   * - Responsive design for mobile compatibility
   * - Custom column rendering
   * - Export functionality
   * - Search and pagination
   */
  if (dt_user_table.length) {
    dt_user = dt_user_table.DataTable({
      // Enable server-side processing for better performance with large datasets
      processing: true,
      serverSide: true,

      // AJAX configuration for data fetching
      ajax: {
        url: baseUrl + 'user-list',
        type: 'GET',
        error: function(xhr, error, thrown) {
          console.error('DataTables AJAX Error:', error);
        }
      },

      // Column definitions - must match server response structure
      columns: [
        { data: '' },                    // Responsive control column
        { data: 'id' },                  // User ID
        { data: 'name' },                // User name
        { data: 'email' },               // User email
        { data: 'email_verified_at' },   // Email verification status
        { data: 'action' }               // Action buttons
      ],

      // Custom column definitions for rendering and behavior
      columnDefs: [
        {
          /**
           * Responsive control column
           * This column contains the expand/collapse button for mobile view
           */
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return ''; // Empty content, styling handled by DataTables responsive
          }
        },
        {
          /**
           * User ID column
           * Displays a formatted ID (could use fake_id for security)
           */
          searchable: false,
          orderable: false,
          targets: 1,
          render: function (data, type, full, meta) {
            return `<span class="fw-medium">${full.fake_id || full.id}</span>`;
          }
        },
        {
          /**
           * User name column with avatar
           * Creates an avatar with user initials and displays the full name
           * Uses random colors for visual variety
           */
          targets: 2,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $name = full['name'];

            // Generate random avatar color from predefined palette
            var stateNum = Math.floor(Math.random() * 7);
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var $state = states[stateNum];

            // Extract initials from full name (first and last name initials)
            var $initialsArr = $name.match(/\b\w/g) || [];
            var $initials = (($initialsArr.shift() || '') + ($initialsArr.pop() || '')).toUpperCase();

            // Create avatar HTML with initials
            var $avatarOutput = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

            // Construct the complete row output with avatar and name
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
                '<div class="avatar-wrapper">' +
                  '<div class="avatar avatar-sm me-3">' +
                    $avatarOutput +
                  '</div>' +
                '</div>' +
                '<div class="d-flex flex-column">' +
                  '<a href="' + userView + '" class="text-body text-truncate text-decoration-none">' +
                    '<span class="fw-semibold">' + $name + '</span>' +
                  '</a>' +
                  // Optional: Add user role or department here
                  // '<small class="text-muted">' + (full.role || '') + '</small>' +
                '</div>' +
              '</div>';

            return $row_output;
          }
        },
        {
          /**
           * Email column
           * Simple email display with mailto link functionality
           */
          targets: 3,
          render: function (data, type, full, meta) {
            var $email = full['email'];
            return '<a href="mailto:' + $email + '" class="user-email text-decoration-none">' + $email + '</a>';
          }
        },
        {
          /**
           * Email verification status column
           * Shows verified/unverified status with appropriate icons
           */
          targets: 4,
          className: 'text-center',
          render: function (data, type, full, meta) {
            var $verified = full['email_verified_at'];

            if ($verified) {
              return '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">' +
                     '<i class="ti ti-shield-check me-1"></i>Verified' +
                     '</span>';
            } else {
              return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">' +
                     '<i class="ti ti-shield-x me-1"></i>Unverified' +
                     '</span>';
            }
          }
        },
        {
          /**
           * Actions column
           * Contains edit, delete, and dropdown menu with additional actions
           */
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          className: 'text-nowrap',
          render: function (data, type, full, meta) {
            return (
              '<div class="d-flex align-items-center gap-1">' +
                // Edit button - opens offcanvas form
                '<button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect edit-record" ' +
                'data-id="' + full['id'] + '" ' +
                'data-bs-toggle="offcanvas" ' +
                'data-bs-target="#offcanvasAddUser" ' +
                'data-bs-original-title="Edit User" ' +
                'title="Edit User">' +
                '<i class="ti ti-edit ti-md"></i>' +
                '</button>' +

                // Delete button - shows confirmation dialog
                '<button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect delete-record" ' +
                'data-id="' + full['id'] + '" ' +
                'data-bs-original-title="Delete User" ' +
                'title="Delete User">' +
                '<i class="ti ti-trash ti-md"></i>' +
                '</button>' +

                // Dropdown menu for additional actions
                '<div class="dropdown">' +
                  '<button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect dropdown-toggle hide-arrow" ' +
                  'data-bs-toggle="dropdown" aria-expanded="false">' +
                  '<i class="ti ti-dots-vertical ti-md"></i>' +
                  '</button>' +
                  '<ul class="dropdown-menu dropdown-menu-end">' +
                    '<li><a class="dropdown-item" href="' + userView + '"><i class="ti ti-eye me-2"></i>View Details</a></li>' +
                    '<li><a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-user-pause me-2"></i>Suspend User</a></li>' +
                    '<li><a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-lock me-2"></i>Reset Password</a></li>' +
                    '<li><hr class="dropdown-divider"></li>' +
                    '<li><a class="dropdown-item text-danger" href="javascript:void(0);"><i class="ti ti-user-x me-2"></i>Deactivate</a></li>' +
                  '</ul>' +
                '</div>' +
              '</div>'
            );
          }
        }
      ],

      // Default ordering - sort by name column in descending order
      order: [[2, 'desc']],

      // Custom DOM structure for better layout control
      dom:
        '<"row mx-1"' +
          '<"col-sm-12 col-md-3"' +
            '<"me-3"l>' + // Length selector
          '>' +
          '<"col-sm-12 col-md-9"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 gap-3"fB>' + // Filter and Buttons
          '>' +
        '>t' + // Table
        '<"row mx-2"' +
          '<"col-sm-12 col-md-6"i>' + // Info
          '<"col-sm-12 col-md-6"p>' + // Pagination
        '>',

      // Localization settings
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search users...',
        info: 'Showing _START_ to _END_ of _TOTAL_ users',
        infoEmpty: 'No users found',
        infoFiltered: '(filtered from _MAX_ total users)',
        paginate: {
          first: 'First',
          last: 'Last',
          next: '<i class="ti ti-chevron-right"></i>',
          previous: '<i class="ti ti-chevron-left"></i>'
        }
      },

      // Button configuration for export and add functionality
      buttons: [
        {
          /**
           * Export dropdown button
           * Provides multiple export options (Print, CSV, Excel, PDF, Copy)
           */
          extend: 'collection',
          className: 'btn btn-outline-secondary dropdown-toggle me-2 waves-effect waves-light',
          text: '<i class="ti ti-download me-1"></i>Export',
          buttons: [
            {
              extend: 'print',
              title: 'MOTAC Users Report',
              text: '<i class="ti ti-printer me-2"></i>Print',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3], // Only export name and email columns
                format: {
                  body: function (inner, coldex, rowdex) {
                    // Remove HTML tags and extract clean text for export
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else if (item.innerText) {
                        result = result + item.innerText;
                      } else if (item.textContent) {
                        result = result + item.textContent;
                      }
                    });
                    return result;
                  }
                }
              },
              customize: function (win) {
                // Customize print layout for better appearance
                $(win.document.body)
                  .css('color', '#333')
                  .css('border-color', '#ddd')
                  .css('background-color', '#fff');
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');

                // Add custom header
                $(win.document.body).prepend(
                  '<div style="text-align: center; margin-bottom: 20px;">' +
                  '<h2>MOTAC Integrated Resource Management System</h2>' +
                  '<h3>Users Report</h3>' +
                  '<p>Generated on: ' + new Date().toLocaleDateString() + '</p>' +
                  '</div>'
                );
              }
            },
            {
              extend: 'csv',
              title: 'MOTAC_Users_' + new Date().toISOString().slice(0, 10),
              text: '<i class="ti ti-file-text me-2"></i>CSV',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                format: {
                  body: function (inner, coldex, rowdex) {
                    return extractTextFromHtml(inner);
                  }
                }
              }
            },
            {
              extend: 'excel',
              title: 'MOTAC_Users_' + new Date().toISOString().slice(0, 10),
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                format: {
                  body: function (inner, coldex, rowdex) {
                    return extractTextFromHtml(inner);
                  }
                }
              }
            },
            {
              extend: 'pdf',
              title: 'MOTAC Users Report',
              text: '<i class="ti ti-file-text me-2"></i>PDF',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                format: {
                  body: function (inner, coldex, rowdex) {
                    return extractTextFromHtml(inner);
                  }
                }
              },
              customize: function (doc) {
                // Customize PDF layout
                doc.content[1].table.widths = ['50%', '50%'];
                doc.styles.tableHeader.fontSize = 12;
                doc.styles.tableHeader.fillColor = '#0066cc';
                doc.styles.tableBodyOdd.fillColor = '#f9f9f9';
              }
            },
            {
              extend: 'copy',
              title: 'MOTAC Users',
              text: '<i class="ti ti-copy me-2"></i>Copy to Clipboard',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                format: {
                  body: function (inner, coldex, rowdex) {
                    return extractTextFromHtml(inner);
                  }
                }
              }
            }
          ]
        },
        {
          /**
           * Add new user button
           * Opens the offcanvas form for creating a new user
           */
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New User</span>',
          className: 'add-new btn btn-primary waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ],

      // Responsive configuration for mobile devices
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'User Details: ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== ''
                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                    '<td class="fw-medium">' + col.title + ':</td> ' +
                    '<td>' + col.data + '</td>' +
                  '</tr>'
                : '';
            }).join('');
            return data ? $('<table class="table table-borderless"/><tbody />').append(data) : false;
          }
        }
      },

      // Additional DataTables configuration
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      scrollX: true,
      autoWidth: false,

      // Callback functions
      initComplete: function() {
        console.log('DataTable initialized successfully');

        // Apply custom styling after initialization
        $('.dataTables_filter input').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('form-select form-select-sm');
      },

      drawCallback: function() {
        // Re-initialize tooltips after each draw
        $('[title]').tooltip();
      }
    });
  }

  // =============================================
  // UTILITY FUNCTIONS
  // =============================================

  /**
   * Extract clean text from HTML content for export functions
   * This function removes HTML tags and extracts readable text
   *
   * @param {string} inner - HTML content to clean
   * @returns {string} - Clean text content
   */
  function extractTextFromHtml(inner) {
    if (inner.length <= 0) return inner;

    var el = $.parseHTML(inner);
    var result = '';

    $.each(el, function (index, item) {
      if (item.classList && item.classList.contains('user-name')) {
        // Extract name from user-name element
        result = result + (item.lastChild ? item.lastChild.textContent : '');
      } else if (item.classList && item.classList.contains('user-email')) {
        // Extract email from user-email element
        result = result + item.textContent;
      } else if (item.innerText) {
        result = result + item.innerText;
      } else if (item.textContent) {
        result = result + item.textContent;
      }
    });

    return result.trim();
  }

  // =============================================
  // EVENT HANDLERS
  // =============================================

  /**
   * Delete User Handler
   * Handles user deletion with confirmation dialog and AJAX request
   * Uses SweetAlert2 for user-friendly confirmation dialogs
   */
  $(document).on('click', '.delete-record', function () {
    var user_id = $(this).data('id'),
        dtrModal = $('.dtr-bs-modal.show');

    // Hide any open responsive modals first
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // Show confirmation dialog using SweetAlert2
    Swal.fire({
      title: 'Are you sure?',
      text: "This action cannot be undone! The user will be permanently deleted.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete user!',
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
        cancelButton: 'btn btn-outline-secondary waves-effect'
      },
      buttonsStyling: false,
      reverseButtons: true
    }).then(function (result) {
      if (result.value) {
        // Show loading state
        Swal.fire({
          title: 'Deleting user...',
          text: 'Please wait while we process your request.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // Perform AJAX DELETE request
        $.ajax({
          type: 'DELETE',
          url: `${baseUrl}user-list/${user_id}`,
          success: function (response) {
            // Refresh the DataTable to reflect changes
            dt_user.draw(false); // false = stay on current page

            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'The user has been successfully deleted.',
              customClass: {
                confirmButton: 'btn btn-success waves-effect waves-light'
              },
              buttonsStyling: false
            });
          },
          error: function (xhr, status, error) {
            console.error('Delete Error:', xhr.responseText);

            // Show error message
            Swal.fire({
              icon: 'error',
              title: 'Deletion Failed',
              text: 'An error occurred while deleting the user. Please try again.',
              customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
              },
              buttonsStyling: false
            });
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // User cancelled the deletion
        Swal.fire({
          title: 'Cancelled',
          text: 'The user deletion has been cancelled.',
          icon: 'info',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      }
    });
  });

  /**
   * Edit User Handler
   * Handles user edit button click, fetches user data, and populates the form
   */
  $(document).on('click', '.edit-record', function () {
    var user_id = $(this).data('id'),
        dtrModal = $('.dtr-bs-modal.show');

    // Hide any open responsive modals
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // Update offcanvas title for edit mode
    $('#offcanvasAddUserLabel').html('<i class="ti ti-user-edit me-2"></i>Edit User');

    // Add loading state to the form
    showFormLoading(true);

    // Fetch user data via AJAX
    $.get(`${baseUrl}user-list/${user_id}/edit`)
      .done(function (data) {
        // Populate form fields with user data
        $('#user_id').val(data.id);
        $('#add-user-fullname').val(data.name);
        $('#add-user-email').val(data.email);
        $('#add-user-contact').val(data.contact || '');
        $('#add-user-company').val(data.company || '');

        // Update Select2 dropdowns if they exist
        if (data.country) {
          $('#add-user-country').val(data.country).trigger('change');
        }

        // Remove loading state
        showFormLoading(false);
      })
      .fail(function (xhr, status, error) {
        console.error('Edit User Fetch Error:', error);

        // Remove loading state and show error
        showFormLoading(false);

        Swal.fire({
          icon: 'error',
          title: 'Error Loading User Data',
          text: 'Unable to load user information. Please try again.',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      });
  });

  /**
   * Add New User Button Handler
   * Resets the form and prepares it for creating a new user
   */
  $('.add-new').on('click', function () {
    // Reset form for new user creation
    $('#user_id').val('');
    $('#offcanvasAddUserLabel').html('<i class="ti ti-user-plus me-2"></i>Add New User');

    // Clear form validation if it exists
    if (typeof fv !== 'undefined') {
      fv.resetForm(true);
    }

    // Reset form fields
    $('#addNewUserForm')[0].reset();

    // Reset Select2 dropdowns
    $('.select2').val(null).trigger('change');
  });

  // =============================================
  // FORM LOADING STATE MANAGEMENT
  // =============================================

  /**
   * Show or hide loading state in the form
   *
   * @param {boolean} show - Whether to show or hide loading state
   */
  function showFormLoading(show) {
    const form = $('#addNewUserForm');
    const submitBtn = form.find('button[type="submit"]');

    if (show) {
      // Show loading state
      form.find('input, select, textarea').prop('disabled', true);
      submitBtn.prop('disabled', true).html('<i class="ti ti-loader-2 ti-spin me-2"></i>Loading...');
    } else {
      // Hide loading state
      form.find('input, select, textarea').prop('disabled', false);
      submitBtn.prop('disabled', false).html('<i class="ti ti-user-plus me-2"></i>Add User');
    }
  }

  // =============================================
  // UI ENHANCEMENTS AND TWEAKS
  // =============================================

  /**
   * Apply UI enhancements after DataTables initialization
   * This includes removing unwanted CSS classes and improving visual appearance
   */
  setTimeout(() => {
    // Remove small form control classes for better consistency
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');

    // Add custom classes for better styling
    $('.dataTables_filter input').addClass('rounded-pill');
    $('.dataTables_length select').addClass('rounded-pill');

    // Enhance search input placeholder
    $('.dataTables_filter input').attr('placeholder', 'Search users by name or email...');
  }, 300);

  // =============================================
  // FORM VALIDATION CONFIGURATION
  // =============================================

  /**
   * Initialize FormValidation plugin for the add/edit user form
   * Provides client-side validation with real-time feedback
   */
  if (addNewUserForm) {
    const fv = FormValidation.formValidation(addNewUserForm, {
      fields: {
        name: {
          validators: {
            notEmpty: {
              message: 'Full name is required'
            },
            stringLength: {
              min: 2,
              max: 100,
              message: 'Name must be between 2 and 100 characters'
            },
            regexp: {
              regexp: /^[a-zA-Z\s]+$/,
              message: 'Name can only contain letters and spaces'
            }
          }
        },
        email: {
          validators: {
            notEmpty: {
              message: 'Email address is required'
            },
            emailAddress: {
              message: 'Please enter a valid email address'
            },
            stringLength: {
              max: 255,
              message: 'Email address is too long'
            }
          }
        },
        userContact: {
          validators: {
            notEmpty: {
              message: 'Contact number is required'
            },
            phone: {
              country: 'MY', // Malaysia
              message: 'Please enter a valid Malaysian phone number'
            }
          }
        },
        company: {
          validators: {
            notEmpty: {
              message: 'Company/Organization is required'
            },
            stringLength: {
              min: 2,
              max: 100,
              message: 'Company name must be between 2 and 100 characters'
            }
          }
        },
        country: {
          validators: {
            notEmpty: {
              message: 'Please select a country'
            }
          }
        }
      },

      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: function (field, ele) {
            return '.mb-3';
          }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus(),

        // Custom plugin for real-time validation feedback
        icon: new FormValidation.plugins.Icon({
          valid: 'ti ti-check',
          invalid: 'ti ti-x',
          validating: 'ti ti-loader-2 ti-spin'
        })
      }
    }).on('core.form.valid', function () {
      /**
       * Handle form submission when validation passes
       * Performs AJAX request to create or update user
       */

      // Get form data
      const formData = $('#addNewUserForm').serialize();
      const isEdit = $('#user_id').val() !== '';

      // Show loading state
      showFormLoading(true);

      // Determine request method and URL
      const method = isEdit ? 'PUT' : 'POST';
      const url = isEdit ? `${baseUrl}user-list/${$('#user_id').val()}` : `${baseUrl}user-list`;

      // Perform AJAX request
      $.ajax({
        data: formData,
        url: url,
        type: method,
        success: function (response) {
          // Hide loading state
          showFormLoading(false);

          // Refresh DataTable
          dt_user.draw(false);

          // Close offcanvas
          offCanvasForm.offcanvas('hide');

          // Show success message
          Swal.fire({
            icon: 'success',
            title: `User ${isEdit ? 'Updated' : 'Created'} Successfully!`,
            text: `The user has been ${isEdit ? 'updated' : 'created'} successfully.`,
            customClass: {
              confirmButton: 'btn btn-success waves-effect waves-light'
            },
            buttonsStyling: false
          });
        },
        error: function (xhr, status, error) {
          // Hide loading state
          showFormLoading(false);

          console.error('Form Submission Error:', xhr.responseText);

          // Parse error response
          let errorMessage = 'An error occurred while saving the user.';

          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          } else if (xhr.status === 422) {
            errorMessage = 'Please check your input data and try again.';
          } else if (xhr.status === 409) {
            errorMessage = 'A user with this email address already exists.';
          }

          // Close offcanvas
          offCanvasForm.offcanvas('hide');

          // Show error message
          Swal.fire({
            title: `${isEdit ? 'Update' : 'Creation'} Failed`,
            text: errorMessage,
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
          });
        }
      });
    });

    // =============================================
    // FORM RESET ON OFFCANVAS HIDE
    // =============================================

    /**
     * Reset form validation and clear fields when offcanvas is hidden
     * Ensures clean state for next form interaction
     */
    offCanvasForm.on('hidden.bs.offcanvas', function () {
      // Reset FormValidation
      fv.resetForm(true);

      // Clear all form fields
      $('#addNewUserForm')[0].reset();

      // Reset Select2 dropdowns
      $('.select2').val(null).trigger('change');

      // Reset form title
      $('#offcanvasAddUserLabel').html('<i class="ti ti-user-plus me-2"></i>Add New User');

      // Remove any loading states
      showFormLoading(false);
    });
  }

  // =============================================
  // INPUT MASKING FOR PHONE NUMBERS
  // =============================================

  /**
   * Apply phone number input masking using Cleave.js
   * Provides automatic formatting for Malaysian phone numbers
   */
  const phoneMaskList = document.querySelectorAll('.phone-mask');

  if (phoneMaskList && window.Cleave) {
    phoneMaskList.forEach(function (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'MY', // Malaysia
        prefix: '+60', // Malaysia country code
        delimiter: ' ',
        blocks: [3, 2, 4, 4], // Format: +60 XX XXXX XXXX
        uppercase: false
      });
    });
  }

  // =============================================
  // ADDITIONAL EVENT LISTENERS
  // =============================================

  /**
   * Handle keyboard shortcuts
   */
  $(document).on('keydown', function(e) {
    // Ctrl+Alt+N = Add new user
    if (e.ctrlKey && e.altKey && e.keyCode === 78) {
      e.preventDefault();
      $('.add-new').click();
    }

    // Escape key = Close any open modals/offcanvas
    if (e.keyCode === 27) {
      if (offCanvasForm.hasClass('show')) {
        offCanvasForm.offcanvas('hide');
      }
    }
  });

  /**
   * Handle real-time search enhancement
   * Debounce search input to reduce server requests
   */
  let searchTimeout;
  $(document).on('keyup', '.dataTables_filter input', function() {
    clearTimeout(searchTimeout);
    const searchTerm = $(this).val();

    searchTimeout = setTimeout(function() {
      // Additional search logic can be added here
      console.log('Searching for:', searchTerm);
    }, 300);
  });

  // =============================================
  // ACCESSIBILITY ENHANCEMENTS
  // =============================================

  /**
   * Enhance accessibility for screen readers and keyboard navigation
   */

  // Add ARIA labels to buttons
  $('.edit-record').attr('aria-label', 'Edit user');
  $('.delete-record').attr('aria-label', 'Delete user');

  // Add role attributes for better screen reader support
  $('.datatables-users').attr('role', 'grid');
  $('.datatables-users thead').attr('role', 'rowgroup');
  $('.datatables-users tbody').attr('role', 'rowgroup');

  // Announce table updates to screen readers
  dt_user_table.on('draw.dt', function() {
    const info = dt_user.page.info();
    const announcement = `Table updated. Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} users.`;

    // Create or update live region for announcements
    let liveRegion = $('#table-announcements');
    if (!liveRegion.length) {
      liveRegion = $('<div id="table-announcements" class="sr-only" aria-live="polite" aria-atomic="true"></div>');
      $('body').append(liveRegion);
    }
    liveRegion.text(announcement);
  });

  // =============================================
  // INITIALIZATION COMPLETE
  // =============================================

  /**
   * Log successful initialization and setup any final configurations
   */
  console.log('✅ MOTAC User Management module initialized successfully');
  console.log('📊 DataTable configured with server-side processing');
  console.log('🔒 CSRF protection enabled for all AJAX requests');
  console.log('✨ Form validation and UI enhancements applied');

  // Dispatch custom event to notify other modules
  $(document).trigger('motac:user-management:ready', {
    dataTable: dt_user,
    formValidation: typeof fv !== 'undefined' ? fv : null,
    timestamp: new Date().toISOString()
  });

}); // End of document ready function

// =============================================
// GLOBAL UTILITY FUNCTIONS
// =============================================

/**
 * Global function to refresh the users DataTable
 * Can be called from other modules or browser console
 */
window.refreshUsersTable = function() {
  if (typeof dt_user !== 'undefined' && dt_user) {
    dt_user.ajax.reload(null, false); // false = stay on current page
    console.log('Users table refreshed');
  } else {
    console.warn('Users DataTable not initialized');
  }
};

/**
 * Global function to get current users table data
 * Useful for debugging or external integrations
 */
window.getUsersTableData = function() {
  if (typeof dt_user !== 'undefined' && dt_user) {
    return dt_user.data().toArray();
  }
  return [];
};
