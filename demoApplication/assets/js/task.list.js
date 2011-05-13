$(document).ready(function() {
  AjaxTaskList('own',    true);
  AjaxTaskList('public', false);
});

function AjaxTaskList(listId, editable)
{
  String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
  }
  
  $('#' + listId + 'ListItemTemplate').template(listId + 'ListItemTemplate');
  $('#' + listId + 'ListErrorTemplate').template(listId + 'ListErrorTemplate');
  $('#' + listId + 'ListPrevLinkTemplate').template(listId + 'ListPrevLinkTemplate');
  $('#' + listId + 'ListNextLinkTemplate').template(listId + 'ListNextLinkTemplate');
  
  $('#' + listId + 'ListPrevLink').live('click', function(e){
    loadPrevListPage();
    
    e.preventDefault();
    return false;
  });
  $('#' + listId + 'ListNextLink').live('click', function(e){
    loadNextListPage();
    
    e.preventDefault();
    return false;
  });
  
  function getCurrentPage()
  {
    return parseInt($('#' + listId + 'ListCurrentPage').text());
  }
  
  function setCurrentPage(page)
  {
    $('#' + listId + 'ListCurrentPage').text(page);
  }
  
  function getTotalPages()
  {
    return parseInt($('#' + listId + 'ListTotalPages').text());
  }
  
  function setTotalPages(page)
  {
    $('#' + listId + 'ListTotalPages').text(page);
  }
  
  function loadPrevListPage()
  {
    if (1 < getCurrentPage())
    {
      loadListPage(getCurrentPage() - 1);
    }
  }

  function loadNextListPage()
  {
    if (getCurrentPage() < getTotalPages())
    {
      loadListPage(getCurrentPage() + 1);
    }
  }

  function loadListPage(page)
  {
    getTasks(page, tasksLoaded(page), tasksLoadError(page));
  }

  function tasksLoaded(page)
  {
    return function(response){
      var tableBodyId = '#' + listId + 'List tbody';
      
      $(tableBodyId).empty();
      setCurrentPage(response.result.currentPage);
      setTotalPages(response.result.totalPages);
      
      manageLinks();
      
      $.each(response.result.tasks, function(index, task) {
        $.tmpl(listId + 'ListItemTemplate', { task: task, editable: editable }).appendTo(tableBodyId);
      });
    }
  }

  function manageLinks()
  {
    $('#' + listId + 'ListPrevLink').remove();
    $('#' + listId + 'ListNextLink').remove();
    
    if (1 < getCurrentPage())
    {
      var link = $.tmpl(listId + 'ListPrevLinkTemplate', { prevPage: getCurrentPage() - 1 });
      $('#' + listId + 'ListPrevLinkParent').append(link);
    }
    
    if (getCurrentPage() < getTotalPages())
    {
      var link = $.tmpl(listId + 'ListNextLinkTemplate', { nextPage: getCurrentPage() + 1 });
      $('#' + listId + 'ListNextLinkParent').append(link);
    }
  }

  function tasksLoadError(page)
  {
    return function(response){
      var tableBodyId = '#' + listId + 'List tbody';
      var counterId   = '#' + listId + 'ListCurrentPage';
      
      $(tableBodyId).empty();
      
      $.tmpl(listId + 'ListErrorTemplate', { editable: editable }).appendTo(tableBodyId);
    };
  }

  function getTasks(page, resultHandler, faultHandler)
  {
      $.jsonRPC.setup({
          endPoint: $('base').attr('href') + 'index.php/rpc/json'
      });
      
      $.jsonRPC.request('TaskService.get' + listId.capitalize() + 'TasksByPage',
      {
          params:  [ page ],
          success: resultHandler,
          error:   faultHandler
      });
  }
}

function formatDate(dateStr)
{
  return dateStr.replace(/^(\d{4})-(\d{2})-(\d{2})$/, '$1. $2. $3.');
}