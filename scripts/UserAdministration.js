define(['IAT','DataTables/media/js/jquery.dataTables'],function (IAT) {
    /*
     * User administration module. This creates an HTML interface to
     * administrate usage of the IAT Manager.
     */
    var UserAdministration = function () {
      return {

        /*
         * Creates and appents a table of users to a given div.
         */
        appendUserTableToDiv : function ($div) {
          IAT.sendRequest(IAT.bundleIATManagerRequestData('getUsers')).done(function (data) {
            data.users = $.map(data.users, function (element,index) {
              var array = [];
              array[0] = element.username;
              if (element.userAdministration === '1')
                array[1] = "<input class='userAdministration' type='checkbox' checked='true'>";
              else
                array[1] = "<input class='userAdministration' type='checkbox'>";
              if (element.active === '1')
                array[2] = "<input class='active' type='checkbox' checked='true'>";
              else
                array[2] = "<input class='active' type='checkbox'>";
              array[3] = element.email;
              array[4] = element.id;
              return [array];
            });
            var tableInfo = {
              aaData : data.users,
              aoColumns : [
              {
                'sTitle':"Username"
              },
              {
                'sTitle':"User Administration"
              },
              {
                'sTitle':"Active"
              },
              {
                'sTitle':"Email"
              }
              ]
            };
            var dataTable = $div.dataTable(tableInfo);
            $('.userAdministration').click(function () {
              var that = this;
              IAT.sendRequest(IAT.bundleIATManagerRequestData("setUserPrivileges",{
                'userAdministration' : $(this).prop('checked'),
                'id' : dataTable.fnGetData($(this).closest('tr')[0])[4]
              })).done(function (data) {
                if (!data.success) {
                  $(that).prop('checked',!$(that).prop('checked'));
                  $.jnotify("Setting user administration privilege failed.");
                }
              });
            });
            $('.active').click(function () {
              var that = this;
              IAT.sendRequest(IAT.bundleIATManagerRequestData("setUserPrivileges",{
                'active' : $(this).prop('checked'),
                'id' : dataTable.fnGetData($(this).closest('tr')[0])[4]
              })).done(function (data) {
                if (!data.success) {
                  $(that).prop('checked',!$(that).prop('checked'));
                  $.jnotify("Setting active failed.");
                }
              });
            });
          });
        }
      };
    }

    return UserAdministration();
});