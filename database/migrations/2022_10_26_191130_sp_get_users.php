<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SpGetUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE procedure sp_get_users
            (
                in pId int,
                in pFullname varchar(50),
                in pEmail varchar(60),
                in pUserType varchar(15),
                in pPerPage int,
                in pPage int
            )
            begin

                declare sqlb, sqlb1, sqlb2 text default '';
                declare cc int;

                select count(distinct(us.id)) into cc
                from users us
                where us.status = 1
                and if(pId is not null and pId <> 0, us.id = pId, true)
                and if(pFullname is not null, us.fullname like concat('%', pFullname, '%'), true)
                and if(pEmail is not null, us.email like concat('%', pEmail, '%'), true)
                and if(pUserType is not null and pUserType <> '', us.user_type = pUserType, true);

                set pPage = pPerPage * (pPage - 1);

                set sqlb1 = concat(\"
                    select
                        \", cc ,\" as cc,
                        us.id,
                        us.fullname,
                        us.email,
                        us.user_type,
                        case
                            when us.user_type = 'admin' then 'ADMINISTRADOR'
                            when us.user_type = 'user' then 'USUARIO'
                        end as `role`,
                        date_format(us.created_at, '%d-%m-%Y') as created_at,

                        \", pPage + 1, \" as page_from,
                        \", pPage + pPerPage, \" as page_to
                    from users us
                    where us.status = 1
                \");

                if pId is not null and pId <> 0 then set sqlb2 = concat(sqlb2, ' and us.id =', pId); end if;
                if pFullname is not null then set sqlb2 = concat(sqlb2, ' and us.fullname like \"%', pFullname, '%\"'); end if;
                if pEmail is not null then set sqlb2 = concat(sqlb2, ' and us.email like \"%', pEmail, '%\"'); end if;
                if pUserType is not null and pUserType <> '' then set sqlb2 = concat(sqlb2, ' and us.user_type = \"', pUserType, '\"'); end if;

                set sqlb = concat(sqlb1, sqlb2);
                set sqlb = concat(sqlb, ' limit ', if(pPerPage = 1, cc, pPerPage), ' offset ', pPage);

                set @sql :=rtrim(sqlb);
                prepare request from @sql;
                execute request;
                deallocate prepare request;

            end;
        ";

        DB::unprepared("DROP procedure if exists sp_get_users");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
