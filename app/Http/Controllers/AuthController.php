<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Token;
use App\Models\User;
use Exception;
use App\Exceptions\MyDbException;
use App\Http\Utils\TokenUtil;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $tokenDI;
    public function __construct(TokenUtil $tokenUtil){
        $this->tokenDI = $tokenUtil;
    }
    /**
     *  로그인처리
     * 
     *  @param Illuminate\Http\Request $request 리퀘스트 객체
     *  @return string json 엑세스토큰 , 쿠키httponly 리플레쉬토큰
     */
    public function login(Request $req){
        // DB 유저정보 획득
        $userInfo = User::where('u_id', $req->u_id)
                        ->where('u_pw',$req->u_pw)
                        ->first();
        // 유저정보 NUll 확인
        if(is_null($userInfo)){
            // 에러 핸들러에 E20으로 리턴
            throw new Exception('E20');
        }
        // 토큰생성
        list($accessToken, $refreshToken) = $this->tokenDI->createTokens($userInfo);

        // 리플래쉬 토큰 DB 저장
        $this->tokenDI->upsertRefreshToken($refreshToken);

        // 리턴
        return response()->json([
            'access_token' => $accessToken
        ],200)->cookie('refresh_token',$refreshToken,env('TOKEN_EXP_REFRESH'));
    }

    /**
     * 엑세스 토큰 재발급
     * 
     * 
     */
    public function reisstoken(Request $request){
        // 리프래쉬 토큰 획득
        $cookieRefreshToken = $request->cookie('refresh_token');

        // 리프래쉬 토큰 체크
        $this->tokenDI->chkToken($cookieRefreshToken);

        // 페이로드에서 u_pk 획득
        $u_pk = $this->tokenDI->getPayloadValueToKey($cookieRefreshToken,'upk');

        // DB 유저정보 획득
        $userInfo = User::where('u_pk',$u_pk)->first();

        // 유저정보 획득 체크
        if(is_null($userInfo)){
            throw new Exception('E20');
        }

        // DB에 저장된 리프래쉬 토큰 검색
        $tokenInfo = Token::select('t_rt','t_ext')
                        ->where('u_pk',$u_pk)
                        ->first();

        // 토큰 정보 획득 체크
        if(is_null($tokenInfo)){
            throw new Exception('E04');
        }

        // 토큰 유효기간 체크
        if(strtotime($tokenInfo->t_ext) < time()){
            throw new Exception('E02');
        }

        // 토큰 일치 체크
        if($cookieRefreshToken !== $tokenInfo->t_rt){
            throw new Exception('E03');
        }
        // 토큰을 새로 작성
        list($accessToken, $refreshToken) = $this->tokenDI->createTokens($userInfo);

        // 리플래쉬 토큰 저장
        $this->tokenDI->upsertRefreshToken($refreshToken);

        // 리턴
        return response()->json([
            'access_token' => $accessToken
        ],200)->cookie('refresh_token',$refreshToken,env('TOKEN_EXP_REFRESH'));
    }
}
