--
-- PostgreSQL database dump
--

-- Dumped from database version 12.0
-- Dumped by pg_dump version 12.0

-- Started on 2019-12-20 02:05:37

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 214 (class 1255 OID 32895)
-- Name: total_order_cost(numeric); Type: FUNCTION; Schema: public; Owner: admin
--

CREATE FUNCTION public.total_order_cost(id numeric) RETURNS money
    LANGUAGE plpgsql
    AS $$
declare total_cost money;
	BEGIN
		with order_price as (select price_per_item*quantity as "price"
		from member_order 
		where unique_id in (select unique_id from club_member where unique_id=id))
		select sum(price) into total_cost from order_price;
		return total_cost;
	END;
$$;


ALTER FUNCTION public.total_order_cost(id numeric) OWNER TO admin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 203 (class 1259 OID 16399)
-- Name: advisor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.advisor (
    advisor_id numeric(5,0) NOT NULL,
    "club_no." numeric(5,0),
    email character varying(200),
    first_name character varying(50) NOT NULL,
    last_name character varying(50) NOT NULL,
    CONSTRAINT advisor_check CHECK ((advisor_id >= (0)::numeric)),
    CONSTRAINT advisor_club_check CHECK (("club_no." >= (0)::numeric)),
    CONSTRAINT advisor_firstname_check CHECK ((char_length((first_name)::text) >= 2)),
    CONSTRAINT advisor_lastname_check CHECK ((char_length((last_name)::text) >= 2)),
    CONSTRAINT advisor_name_check CHECK ((char_length((first_name)::text) >= 2))
);


ALTER TABLE public.advisor OWNER TO postgres;

--
-- TOC entry 2924 (class 0 OID 0)
-- Dependencies: 203
-- Name: TABLE advisor; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.advisor IS 'Stores information pertaining to an advisor of a specific club.';


--
-- TOC entry 2925 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN advisor.advisor_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.advisor.advisor_id IS 'PK';


--
-- TOC entry 208 (class 1259 OID 16449)
-- Name: bus_pass; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bus_pass (
    unique_id numeric(5,0),
    "check#" character varying(12),
    amount_paid money NOT NULL,
    b_pass_id integer NOT NULL,
    CONSTRAINT bus_pass_check_num_check CHECK ((char_length(("check#")::text) >= 4)),
    CONSTRAINT bus_pass_member_check CHECK ((unique_id >= (0)::numeric)),
    CONSTRAINT bus_pass_money_check CHECK (((amount_paid)::numeric > 0.0))
);


ALTER TABLE public.bus_pass OWNER TO postgres;

--
-- TOC entry 2926 (class 0 OID 0)
-- Dependencies: 208
-- Name: TABLE bus_pass; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.bus_pass IS 'Holds the bus pass of a member';


--
-- TOC entry 210 (class 1259 OID 24703)
-- Name: bus_pass_b_pass_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bus_pass_b_pass_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bus_pass_b_pass_id_seq OWNER TO postgres;

--
-- TOC entry 2927 (class 0 OID 0)
-- Dependencies: 210
-- Name: bus_pass_b_pass_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bus_pass_b_pass_id_seq OWNED BY public.bus_pass.b_pass_id;


--
-- TOC entry 204 (class 1259 OID 16409)
-- Name: club_member; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.club_member (
    unique_id numeric(5,0) NOT NULL,
    "club_no." numeric(5,0),
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    needs_bus_pass boolean,
    is_student boolean,
    CONSTRAINT club_member_check CHECK ((unique_id >= (0)::numeric)),
    CONSTRAINT club_member_club_check CHECK (("club_no." >= (0)::numeric))
);


ALTER TABLE public.club_member OWNER TO postgres;

--
-- TOC entry 2928 (class 0 OID 0)
-- Dependencies: 204
-- Name: TABLE club_member; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.club_member IS 'Holds data for a club member';


--
-- TOC entry 205 (class 1259 OID 16419)
-- Name: form; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.form (
    form_type character varying(100) NOT NULL,
    unique_id numeric(5,0) NOT NULL,
    completion_date date,
    CONSTRAINT form_member_check CHECK ((unique_id >= (0)::numeric))
);


ALTER TABLE public.form OWNER TO postgres;

--
-- TOC entry 2929 (class 0 OID 0)
-- Dependencies: 205
-- Name: TABLE form; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.form IS 'Hold an artifact denoting the completion of a from';


--
-- TOC entry 2930 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN form.form_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.form.form_type IS 'could be emergency form or waiver form';


--
-- TOC entry 206 (class 1259 OID 16429)
-- Name: medical_insurance; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.medical_insurance (
    "member_ID/policy_number" bigint NOT NULL,
    unique_id numeric(5,0) NOT NULL,
    plan_name character varying(150),
    pharmacy_network character varying(50),
    group_number integer,
    CONSTRAINT medical_insurance_group_check CHECK ((group_number >= 0)),
    CONSTRAINT medical_insurance_member_check CHECK ((unique_id >= (0)::numeric)),
    CONSTRAINT medical_insurance_policy_num_check CHECK (("member_ID/policy_number" >= 0))
);


ALTER TABLE public.medical_insurance OWNER TO postgres;

--
-- TOC entry 2931 (class 0 OID 0)
-- Dependencies: 206
-- Name: TABLE medical_insurance; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.medical_insurance IS 'Holds the information on a members insurance card';


--
-- TOC entry 207 (class 1259 OID 16439)
-- Name: member_order; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.member_order (
    unique_id numeric(5,0),
    item_type character varying(50) NOT NULL,
    custom_name_label character varying(75),
    size character varying(10) NOT NULL,
    color character varying(50) NOT NULL,
    quantity smallint NOT NULL,
    price_per_item money NOT NULL,
    order_id integer NOT NULL,
    CONSTRAINT member_order_member_check CHECK ((unique_id >= (0)::numeric)),
    CONSTRAINT member_order_price_check CHECK (((price_per_item)::numeric > (0)::numeric)),
    CONSTRAINT member_order_quantity_check CHECK ((quantity > 0))
);


ALTER TABLE public.member_order OWNER TO postgres;

--
-- TOC entry 2932 (class 0 OID 0)
-- Dependencies: 207
-- Name: TABLE member_order; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.member_order IS 'Holds information for an individual order placed by a member';


--
-- TOC entry 2933 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN member_order.custom_name_label; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.member_order.custom_name_label IS 'Length actually reflects the length of characters allowed for the label';


--
-- TOC entry 2934 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN member_order.size; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.member_order.size IS 'Small, medium, or large are examples';


--
-- TOC entry 211 (class 1259 OID 24716)
-- Name: member_order_order_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.member_order_order_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.member_order_order_id_seq OWNER TO postgres;

--
-- TOC entry 2935 (class 0 OID 0)
-- Dependencies: 211
-- Name: member_order_order_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.member_order_order_id_seq OWNED BY public.member_order.order_id;


--
-- TOC entry 209 (class 1259 OID 16461)
-- Name: pass; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pass (
    pass_id numeric(5,0) NOT NULL,
    unique_id numeric(5,0),
    pass_days_validity character varying(100) NOT NULL,
    type character varying(100) NOT NULL,
    CONSTRAINT pass_check CHECK ((pass_id >= (0)::numeric)),
    CONSTRAINT pass_member_check CHECK ((unique_id >= (0)::numeric))
);


ALTER TABLE public.pass OWNER TO postgres;

--
-- TOC entry 2936 (class 0 OID 0)
-- Dependencies: 209
-- Name: TABLE pass; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.pass IS 'Holds the information pertaining to a members pass';


--
-- TOC entry 202 (class 1259 OID 16394)
-- Name: ski_club; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ski_club (
    "club_no." numeric(5,0) NOT NULL,
    club_name character varying(100) NOT NULL,
    max_bus_participants smallint,
    desired_resort character varying(100),
    CONSTRAINT ski_club_bus_check CHECK ((max_bus_participants >= 1)),
    CONSTRAINT ski_club_check CHECK (("club_no." >= (0)::numeric))
);


ALTER TABLE public.ski_club OWNER TO postgres;

--
-- TOC entry 2937 (class 0 OID 0)
-- Dependencies: 202
-- Name: TABLE ski_club; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.ski_club IS 'Stores information pertaining to a ski club ';


--
-- TOC entry 2938 (class 0 OID 0)
-- Dependencies: 202
-- Name: COLUMN ski_club."club_no."; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ski_club."club_no." IS 'PK';


--
-- TOC entry 213 (class 1259 OID 32898)
-- Name: users; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username text NOT NULL,
    password text NOT NULL
);


ALTER TABLE public.users OWNER TO admin;

--
-- TOC entry 2939 (class 0 OID 0)
-- Dependencies: 213
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON TABLE public.users IS 'Used to handle user credentials';


--
-- TOC entry 2940 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN users.id; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.users.id IS 'Auto increment';


--
-- TOC entry 2941 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN users.password; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.users.password IS 'The passwords are stored using hash algorithms';


--
-- TOC entry 212 (class 1259 OID 32896)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO admin;

--
-- TOC entry 2942 (class 0 OID 0)
-- Dependencies: 212
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 2742 (class 2604 OID 24705)
-- Name: bus_pass b_pass_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bus_pass ALTER COLUMN b_pass_id SET DEFAULT nextval('public.bus_pass_b_pass_id_seq'::regclass);


--
-- TOC entry 2738 (class 2604 OID 24718)
-- Name: member_order order_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member_order ALTER COLUMN order_id SET DEFAULT nextval('public.member_order_order_id_seq'::regclass);


--
-- TOC entry 2748 (class 2604 OID 32901)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 2908 (class 0 OID 16399)
-- Dependencies: 203
-- Data for Name: advisor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.advisor (advisor_id, "club_no.", email, first_name, last_name) FROM stdin;
1	1	real@gmail.com	Rick	Wester
2	2	jimmy@gmail.com	Jimmy	Real
3	1	bob@bob.com	Bob	Oscar 
\.


--
-- TOC entry 2913 (class 0 OID 16449)
-- Dependencies: 208
-- Data for Name: bus_pass; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bus_pass (unique_id, "check#", amount_paid, b_pass_id) FROM stdin;
3	2121	$80.00	6
4	1227	$80.00	7
\.


--
-- TOC entry 2909 (class 0 OID 16409)
-- Dependencies: 204
-- Data for Name: club_member; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.club_member (unique_id, "club_no.", first_name, last_name, needs_bus_pass, is_student) FROM stdin;
1	1	Jim	Smith	f	f
2	1	Carol	West	f	t
3	2	Nick 	Goepper	t	f
4	3	Terra	West	t	t
6	1	Landon	Shenberger	t	t
7	1	Alex	Pager	f	t
8	5	Tom	Lee	f	f
\.


--
-- TOC entry 2910 (class 0 OID 16419)
-- Dependencies: 205
-- Data for Name: form; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.form (form_type, unique_id, completion_date) FROM stdin;
Waiver Form	1	2019-12-20
Emergency Form	1	2019-12-20
Emergency Form	4	2019-12-20
\.


--
-- TOC entry 2911 (class 0 OID 16429)
-- Dependencies: 206
-- Data for Name: medical_insurance; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.medical_insurance ("member_ID/policy_number", unique_id, plan_name, pharmacy_network, group_number) FROM stdin;
12345	1	Blue Anthem 	Rx	\N
136783	4	Blue Anthem 	Rx	23
32313	3	Fake Insurance 	Rx	234
234131	2	e-health	Rx	\N
72121	7	e-health	Rx	\N
\.


--
-- TOC entry 2912 (class 0 OID 16439)
-- Dependencies: 207
-- Data for Name: member_order; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.member_order (unique_id, item_type, custom_name_label, size, color, quantity, price_per_item, order_id) FROM stdin;
1	T-Shirt Long Sleeve	I love databases	Large	Light Grey	2	$35.99	10
1	Hoodie	:D	Large	Light Grey	1	$70.00	11
1	T-Shirt Short Sleeve	\N	Large	Red	1	$15.99	12
\.


--
-- TOC entry 2914 (class 0 OID 16461)
-- Dependencies: 209
-- Data for Name: pass; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pass (pass_id, unique_id, pass_days_validity, type) FROM stdin;
47585	1	Every Day Pass	Lift Ticket w/ Lessons & Rentals
14	4	Every Day Pass	Lift Ticket Only
\.


--
-- TOC entry 2907 (class 0 OID 16394)
-- Dependencies: 202
-- Data for Name: ski_club; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ski_club ("club_no.", club_name, max_bus_participants, desired_resort) FROM stdin;
1	MVNU	36	Mad River
2	Whitefish High School 	120	Whitefish Mountain Resort  
3	Mount Vernon Ski Club 	89	Snow Trails
4	The Racers	\N	Breckenridge Ski Resort 
5	Princeton University	640	Mountain Creek 
6	High valley Ski Club 	89	\N
\.


--
-- TOC entry 2918 (class 0 OID 32898)
-- Dependencies: 213
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.users (id, username, password) FROM stdin;
19	fakeuser1	$2y$10$hMHUPmnGuD6tzxntQDsNoOqwav.a5omPEDOw7h.RDV5DvnqzADjNy
21	red@80	$2y$10$8Zz4nuCXWF9WVNbEh1Y72.HsOqNozs6tgJKbmwOHzF2jhjelYD72.
\.


--
-- TOC entry 2943 (class 0 OID 0)
-- Dependencies: 210
-- Name: bus_pass_b_pass_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bus_pass_b_pass_id_seq', 7, true);


--
-- TOC entry 2944 (class 0 OID 0)
-- Dependencies: 211
-- Name: member_order_order_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.member_order_order_id_seq', 12, true);


--
-- TOC entry 2945 (class 0 OID 0)
-- Dependencies: 212
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.users_id_seq', 21, true);


--
-- TOC entry 2753 (class 2606 OID 16403)
-- Name: advisor advisor_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.advisor
    ADD CONSTRAINT advisor_pk PRIMARY KEY (advisor_id);


--
-- TOC entry 2766 (class 2606 OID 24726)
-- Name: bus_pass bus_pass_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bus_pass
    ADD CONSTRAINT bus_pass_pk PRIMARY KEY (b_pass_id);


--
-- TOC entry 2757 (class 2606 OID 16413)
-- Name: club_member club_member_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.club_member
    ADD CONSTRAINT club_member_pk PRIMARY KEY (unique_id);


--
-- TOC entry 2759 (class 2606 OID 16423)
-- Name: form form_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.form
    ADD CONSTRAINT form_pk PRIMARY KEY (form_type, unique_id);


--
-- TOC entry 2761 (class 2606 OID 24710)
-- Name: medical_insurance medical_insurance_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medical_insurance
    ADD CONSTRAINT medical_insurance_pk PRIMARY KEY ("member_ID/policy_number", unique_id);


--
-- TOC entry 2763 (class 2606 OID 24724)
-- Name: member_order member_order_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member_order
    ADD CONSTRAINT member_order_pk PRIMARY KEY (order_id);


--
-- TOC entry 2768 (class 2606 OID 16465)
-- Name: pass pass_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pass
    ADD CONSTRAINT pass_pk PRIMARY KEY (pass_id);


--
-- TOC entry 2750 (class 2606 OID 16398)
-- Name: ski_club ski_club_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ski_club
    ADD CONSTRAINT ski_club_pk PRIMARY KEY ("club_no.");


--
-- TOC entry 2771 (class 2606 OID 32912)
-- Name: users username_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT username_unique UNIQUE (username);


--
-- TOC entry 2773 (class 2606 OID 32906)
-- Name: users users_pk; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);


--
-- TOC entry 2751 (class 1259 OID 16472)
-- Name: advisor_club_no__idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX advisor_club_no__idx ON public.advisor USING btree ("club_no.");


--
-- TOC entry 2754 (class 1259 OID 16460)
-- Name: club_member_club_no__idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX club_member_club_no__idx ON public.club_member USING btree ("club_no.");


--
-- TOC entry 2755 (class 1259 OID 16473)
-- Name: club_member_needs_bus_pass_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX club_member_needs_bus_pass_idx ON public.club_member USING btree (needs_bus_pass);


--
-- TOC entry 2764 (class 1259 OID 16459)
-- Name: member_order_unique_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX member_order_unique_id_idx ON public.member_order USING btree (unique_id);


--
-- TOC entry 2769 (class 1259 OID 16471)
-- Name: pass_unique_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pass_unique_id_idx ON public.pass USING btree (unique_id);


--
-- TOC entry 2774 (class 2606 OID 16404)
-- Name: advisor advisor_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.advisor
    ADD CONSTRAINT advisor_fk FOREIGN KEY ("club_no.") REFERENCES public.ski_club("club_no.") ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2946 (class 0 OID 0)
-- Dependencies: 2774
-- Name: CONSTRAINT advisor_fk ON advisor; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON CONSTRAINT advisor_fk ON public.advisor IS 'FK referencing ski club number';


--
-- TOC entry 2779 (class 2606 OID 16454)
-- Name: bus_pass bus_pass_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bus_pass
    ADD CONSTRAINT bus_pass_fk FOREIGN KEY (unique_id) REFERENCES public.club_member(unique_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2775 (class 2606 OID 16414)
-- Name: club_member club_member_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.club_member
    ADD CONSTRAINT club_member_fk FOREIGN KEY ("club_no.") REFERENCES public.ski_club("club_no.") ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2776 (class 2606 OID 16424)
-- Name: form form_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.form
    ADD CONSTRAINT form_fk FOREIGN KEY (unique_id) REFERENCES public.club_member(unique_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2777 (class 2606 OID 16434)
-- Name: medical_insurance medical_insurance_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medical_insurance
    ADD CONSTRAINT medical_insurance_fk FOREIGN KEY (unique_id) REFERENCES public.club_member(unique_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2778 (class 2606 OID 16444)
-- Name: member_order member_order_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.member_order
    ADD CONSTRAINT member_order_fk FOREIGN KEY (unique_id) REFERENCES public.club_member(unique_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2780 (class 2606 OID 16466)
-- Name: pass pass_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pass
    ADD CONSTRAINT pass_fk FOREIGN KEY (unique_id) REFERENCES public.club_member(unique_id) ON UPDATE CASCADE ON DELETE SET NULL;


-- Completed on 2019-12-20 02:05:38

--
-- PostgreSQL database dump complete
--

