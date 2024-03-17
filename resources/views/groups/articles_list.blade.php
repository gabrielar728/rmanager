<style>
    body.dragging, body.dragging * {
        cursor: move !important;
    }

    .dragged {
        position: absolute;
        opacity: 0.5;
        z-index: 2000;
    }

    ol.example li.placeholder {
        position: relative;
        /** More li styles **/
    }
    ol.example li.placeholder:before {
        position: absolute;
        /** Define arrowhead **/
    }
    ol li{
        list-style-type: none;
        color: #606060;
    }
    hr {
        margin-top: 5px;
        margin-bottom: 5px;
        border: 0;
        border-top: 1px solid #dddddd;
        width: 90%;
    }
</style>
<strong style="padding-left: 50px; color: #606060; font-size: 16px; margin-top: 15px;"> # </strong><strong style="color: #606060; margin-top: 15px;"> Articol </strong>
<hr align="left" style="border-top: 1px solid #606060; width: 91.6%">
<ol class='example'>
    @foreach($articles_groups as $articles_group)
        <li data-id="{{ $articles_group->article_id }}" class="list{{$id}}">
            <i class="fa fa-arrows" style="padding-left: 5px; font-size: 14px;">
            </i> {{ $articles_group->article['name'] }}
            <hr align="left">
        </li>
    @endforeach
</ol>
<button class="btn btn-primary btn-sm sortGroup" id="{{ $id }}" style="margin-left: 45px;">Salveaza Ordonarea</button>
<button class="btn btn-danger btn-sm closeButton" id="{{ $id }}" style="">Inchide</button>

<script>
    $(function  () {
        $("ol.example").sortable();
    });

    $('.sortGroup').each(function() {
        $(this).click(function(){
            var id = $(this).attr('id');
            var values_id = $(".list"+id).map(function() { return $(this).data("id"); }).get();
            var data = {'id':id, 'values_id':values_id};
            $.post("{{ route('group.saveSorting') }}", data, function (data) {
                $("#group_sort"+id).hide();
            });
        });
    });
    //------------------------------------------------------------
    $('.closeButton').each(function() {
        $(this).click(function(){
            var id = $(this).attr('id');
            $("#group_sort"+id).hide();
        });
    });
</script>