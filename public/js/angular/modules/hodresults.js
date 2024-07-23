app.controller("HODResultsController", function ($scope) {
    $scope.pending_results = [];
    $scope.approved_results = [];
    $scope.initialized = false;
    $scope.view_course_results = [];
    $scope.search_input = null;
    $scope.active_nav='approved';
    $scope.sorting = {
        attr: "id",
        order: "ASC",
    };

    $scope.initializePage = () => {

        $scope.Route.scope($scope);

        $scope.api(
            "/app/hod/results/index",
            {},
            (res) => {
                $scope.pending_results = Object.values(res.pendingResults);
                $scope.approved_results = Object.values(res.approvedResults);
                $scope.initialized = true;
            },
            (error) => {
                $scope.initialized = true;
            }
        );

        $scope.Route.restore(function(data){
            console.log({data});
        })
    };

    $scope.SearchIn = (text) => {
        $scope.api(
            "/app/hod/results/index",
            {
                search: text,
            },
            (res) => {
                $scope.pending_results = Object.values(res.pendingResults);
                $scope.approved_results = Object.values(res.approvedResults);
                $scope.initialized = true;
            },
            (error) => {
                $scope.initialized = true;
            }
        );
    };

    $scope.sortResults = () => {
        $scope.api(
            "/app/hod/results/index",
            {
                sort: [$scope.sorting.attr, $scope.sorting.order],
            },
            (res) => {
                $scope.pending_results = Object.values(res.pendingResults);
                $scope.approved_results = Object.values(res.approvedResults);
                $scope.initialized = true;
            },
            (error) => {
                $scope.initialized = true;
            }
        );
    };

    $scope.approveResult = (result) => {
        return $scope.api({
            askPin: true,
            url: "/app/hod/results/approve",
            data: {
                results_id: result.reference_id,
            },
            success: (res) => {
                // $scope.pending_results = res.data.pendingResults;
                // $scope.approved_results = res.data.approvedResults;
                $scope.initializePage();
            }
        });
    };

   


    $scope.disapproveResult = (result) => {
        return $scope.api({
            askPin: true,
            url: "/app/hod/results/disapprove",
            data: {
                results_id: result.reference_id,
            },
            success: (res) => {
                // $scope.pending_results = res.data.pendingResults;
                // $scope.approved_results = res.data.approvedResults;
                $scope.initializePage();
            }
       } );
    };

    $scope.viewResults = (results) => {
        $scope.view_course_results = results;
       
        $scope.route("view_results", [
            'active_nav',
            'view_course_results'
        ]);
    };
});
