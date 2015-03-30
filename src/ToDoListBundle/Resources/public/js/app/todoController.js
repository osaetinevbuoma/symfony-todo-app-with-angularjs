"use strict";

(function() {
    var todoController = angular.module("todoController", []);

    /**
     * Save new or edit old todo item
     */
    todoController.controller("TodoListController", ["$http", "$scope", function($http, $scope) {
        var todoListCtrl = this;

        // Convert string to date
        todoListCtrl.convertDate = function(dateString) {
            return new Date(dateString);
        };

        // Format completed string
        todoListCtrl.isCompleted = function (isCompleted) {
            if (isCompleted == true) return "Completed";
            else return "Not Completed";
        };

        // List all todo items
        $http.get("/list").success(function(data) {
            $scope.todos = data;
        });

        /**
         * Save todo object
         */
        todoListCtrl.saveTodo = function (id) {
            if (null == id) {
                $http.post("/save", $scope.todo)
                    .success(function(data) {
                        $scope.todo.id = data;
                        $scope.todo.isCompleted = false;
                        $scope.todo.startDate.date = $scope.todo.startDate;
                        $scope.todo.endDate.date = $scope.todo.endDate;
                        $scope.todos.push($scope.todo);
                        $scope.todo = {};
                        console.log(data);
                    })
                    .error(function(data) {
                        console.log(data);
                    });
            } else {
                $http.put("/update", $scope.todo).success(function (data) {
                    if (200 == data) {
                        // not very good in getting the array index
                        for (var i = 0; i < $scope.todos.length; i++) {
                            if ($scope.todos[i].id == $scope.todo.id) {
                                $scope.todo.startDate.date = $scope.todo.startDate;
                                $scope.todo.endDate.date = $scope.todo.endDate;
                                $scope.todos[i] = $scope.todo;
                                break;
                            }
                        }
                    }
                });
            }
        };

        /**
         * Fetch todo item to edit
         * @param id The id of the item
         */
        todoListCtrl.editTodo = function(id) {
            $http.get("/edit/" + id).success(function(data) {
                var data = {
                    id: data[0].id,
                    title: data[0].title,
                    description: data[0].description,
                    startDate: todoListCtrl.convertDate(data[0].startDate.date),
                    endDate: todoListCtrl.convertDate(data[0].endDate.date),
                    isCompleted: data[0].isCompleted
                };

                $scope.todo = data;
            });
        };

        /**
         * Delete todo item
         */
        todoListCtrl.deleteTodo = function (id) {
            $http.delete("/delete/" + id).success(function(data) {
                if (200 == data) {
                    var oldTodos = [];

                    // not very good in getting the array index
                    for (var i = 0; i < $scope.todos.length; i++) {
                        if ($scope.todos[i].id != $scope.todo.id) {
                            oldTodos.push($scope.todos[i]);
                        }
                    }

                    $scope.todo = {};
                    $scope.todos = [];
                    $scope.todos = oldTodos.concat();
                }
            });
        };

        // Cancel edit or delete
        todoListCtrl.cancel = function () {
            $scope.todo = {};
        }
    }]);
})();